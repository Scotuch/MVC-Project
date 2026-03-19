<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Veritabanı Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/
class Veritabani extends PDO
{
    private $kod;
    private $kod2;
    private $veritabaniAdi;
    private $tabloAdi;
    private $whereParams = [];
    private $transactionActive = false;

    public function __construct()
    {
        try {
            parent::__construct('mysql:host=' . VT_HOST . ';port=' . VT_PORT . ';dbname=' . VT_ADI, VT_KULLANICI, VT_SIFRE);
            $this->veritabaniAdi = VT_ADI;
            $this->kod2 = null;
            $this->query('SET CHARACTER SET utf8');
            $this->query('SET NAMES utf8');
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            Log::Basari(0, "Veritabanı bağlantısı başarıyla kuruldu: " . VT_ADI, GELISTIRICI);
        } catch (PDOException $e) {
            Log::Hata(0, "Veritabanı bağlantı hatası: " . $e->getMessage());
            Hata($e);
        }
    }

    private function filtrele($isim)
    {
        if (preg_match('/^[a-zA-Z0-9_]+$/', $isim)) {
            return $isim;
        }
        Log::Hata(0, "Güvenlik: Geçersiz kolon adı tespit edildi: " . htmlspecialchars($isim, ENT_QUOTES, 'UTF-8'));
        throw new Exception("Geçersiz kolon adı: $isim");
    }

    public function kod($kod)
    {
        $this->kod = $kod;
        return $this;
    }

    public function sec($tablo)
    {
        $tablo = $this->filtrele($tablo);
        $this->kod = "SELECT * FROM $tablo";
        $this->tabloAdi = $tablo;
        return $this;
    }

    public function sil($tablo)
    {
        $this->kod = "DELETE FROM $tablo";
        $this->tabloAdi = $tablo;
        return $this;
    }

    public function guncelle($tablo)
    {
        $this->kod = "UPDATE $tablo";
        $this->tabloAdi = $tablo;
        return $this;
    }

    public function say($tablo)
    {
        $this->kod = "SELECT COUNT(*) FROM $tablo";
        $this->tabloAdi = $tablo;
        try {
            $sorgu = $this->prepare($this->kod);
            $sorgu->execute();
            $say = $sorgu->fetchColumn();
            return $say;
        } catch (PDOException $hata) {
            return 0;
        }
    }

    /**
     * WHERE koşulu ekler (prepared statement ile güvenli)
     * @param string $data Kolon adı
     * @param mixed $value Değer
     * @param string $operator Karşılaştırma operatörü (=, >, <, >=, <=, !=, LIKE)
     * @return $this
     */
    public function where($data, $value, $operator = '=')
    {
        $data = $this->filtrele($data);
        $operator = strtoupper($operator);
        $allowedOperators = ['=', '>', '<', '>=', '<=', '!=', '<>', 'LIKE'];

        if (!in_array($operator, $allowedOperators)) {
            Log::Hata(0, "Geçersiz operatör (where): $operator");
            throw new Exception("Geçersiz operatör: $operator");
        }

        $paramName = $data . '_' . uniqid();
        $this->kod .= " WHERE $data $operator :$paramName";
        $this->whereParams[$paramName] = $value;
        return $this;
    }

    public function orderBy($kolon_adi, $sort = 'ASC')
    {
        $this->kod .= ' ORDER BY ' . $kolon_adi . ' ' . $sort;
        return $this;
    }

    public function limit($limit)
    {
        $this->kod .= ' LIMIT ' . $limit;
        return $this;
    }

    /**
     * Arama yapar (prepared statement ile güvenli)
     * @param string $id Kolon adı
     * @param string $aranan Aranacak değer
     * @param int $limit Sonuç limiti
     * @return $this
     */
    public function ara($id, $aranan, $limit = 50)
    {
        $id = $this->filtrele($id);
        $paramName = $id . '_ara_' . uniqid();
        $this->kod .= " WHERE {$id} LIKE :$paramName LIMIT $limit";
        $this->whereParams[$paramName] = '%' . $aranan . '%';
        return $this;
    }

    public function ekle($tablo)
    {
        $this->kod = 'INSERT INTO ' . $tablo;
        $this->tabloAdi = $tablo;
        return $this;
    }

    public function veri($data, $value = null)
    {
        if ($value) {
            if (strstr($value, '+')) {
                $this->kod .= ' SET ' . $data . ' = ' . $data . ' ' . $value;
                $executeValue = null;
            } elseif (strstr($value, '-')) {
                $this->kod .= ' SET ' . $data . ' = ' . $data . ' ' . $value;
                $executeValue = null;
            } else {
                $this->kod .= ' SET ' . $data . ' = :' . $data . '';
                $executeValue = [
                    $data => $value
                ];
            }
        } else {

            $this->kod .= ' SET ' . implode(', ', array_map(function ($item) {
                return $item . ' = :' . $item;
            }, array_keys($data)));
            $executeValue = $data;
        }
        $this->kod2 = $executeValue;
        return $this;
    }

    public function tabloOlustur($tablo, $array)
    {
        if ($tablo == '' || !is_array($array)) {
            return false;
        }
        $this->kod = "CREATE table $tablo ";
        $this->tabloAdi = $tablo;
        $query = '';
        foreach ($array as $a => $b) {
            $b = mb_strtoupper($b);
            if ($b == 'VARCHAR') {
                $b .= '(255)';
            }
            $query .= "$a $b  NOT NULL, ";
        }

        $query = substr($query, 0, -2);
        $this->kod .= "($query);";
        return $this;
    }

    public function tabloSil($tablo)
    {
        $this->kod = "DROP table $tablo ";
        $this->tabloAdi = $tablo;
        return $this;
    }

    public function tabloBosalt($tablo)
    {
        $this->kod = "TRUNCATE $tablo ";
        $this->tabloAdi = $tablo;
        return $this;
    }

    public function tabloKontrol()
    {
        $this->kod .= " LIMIT 1";
        $this->kod = str_replace('SELECT * ', 'SELECT 1 ', $this->kod);
        try {
            $result = $this->query($this->kod);
        } catch (Exception $e) {
            Log::Hata(0, "varMi() sorgu hatası: " . $e->getMessage());
            return false;
        }

        return $result !== false;
    }

    /**
     * Sorguyu çalıştırır ve sonuç döndürür
     * @param string $fetch 'coklu', 'tekli', 'say' veya boş
     * @return mixed Sonuç veya $this
     */
    public function calistir($fetch = '')
    {
        try {
            // whereParams ve kod2'yi birleştir
            $allParams = array_merge(
                (array)$this->kod2,
                $this->whereParams
            );

            Log::Bilgi(0, "SQL Sorgusu Calistiriliyor | SQL: " . $this->kod . " | Parametreler: " . json_encode($allParams), GELISTIRICI);
            $sorgu = $this->prepare($this->kod);
            $calistiMi = $sorgu->execute($allParams);

            // Etkilenen satır sayısını al
            $etkilenenSatir = $sorgu->rowCount();

            // Parametreleri temizle
            $this->whereParams = [];

            if ($fetch == 'coklu') {
                $sonuc = $sorgu->fetchAll(PDO::FETCH_OBJ);
                Log::Basari(0, "SQL Sorgusu Basarili (Coklu) | Kayit Sayisi: " . count($sonuc) . " | Etkilenen: $etkilenenSatir", GELISTIRICI);
                return $sonuc;
            }
            if ($fetch == 'tekli') {
                $sonuc = $sorgu->fetch(PDO::FETCH_OBJ);
                Log::Basari(0, "SQL Sorgusu Basarili (Tekli) | Etkilenen: $etkilenenSatir", GELISTIRICI);
                return $sonuc;
            }
            if ($fetch == 'say') {
                Log::Basari(0, "SQL Sorgusu Basarili | Etkilenen Satir: $etkilenenSatir", GELISTIRICI);
                return $etkilenenSatir;
            }

            // INSERT, UPDATE, DELETE için etkilenen satır sayısı önemli
            if ($etkilenenSatir > 0 || $calistiMi) {
                Log::Basari(0, "SQL Sorgusu Basarili | Etkilenen Satir: $etkilenenSatir", GELISTIRICI);
            } else {
                Log::Dikkat(0, "SQL Sorgusu Calisti Ama Hicbir Satir Etkilenmedi | SQL: " . $this->kod, GELISTIRICI);
            }

            return $this;
        } catch (PDOException $hata) {
            Log::Hata(0, "Veritabani Hatasi: " . $hata->getMessage() . " | SQL: " . $this->kod);
            return $hata;
        }
    }

    /**
     * Veritabanı bağlantı durumunu ve istatistiklerini döndürür
     * @param bool $detayli Detaylı bilgi isteniyor mu
     * @return array Durum bilgileri
     */
    public function durum($detayli = false)
    {
        $durum = [
            'baglanti' => [
                'client_version' => $this->getAttribute(PDO::ATTR_CLIENT_VERSION),
                'connection_status' => $this->getAttribute(PDO::ATTR_CONNECTION_STATUS),
                'server_info' => $this->getAttribute(PDO::ATTR_SERVER_INFO),
                'server_version' => $this->getAttribute(PDO::ATTR_SERVER_VERSION),
                'driver_name' => $this->getAttribute(PDO::ATTR_DRIVER_NAME),
            ],
            'ayarlar' => [
                'veritabani_adi' => $this->veritabaniAdi,
                'karakter_seti' => $this->query('SELECT @@character_set_database')->fetchColumn(),
                'collation' => $this->query('SELECT @@collation_database')->fetchColumn(),
                'timezone' => $this->query('SELECT @@session.time_zone')->fetchColumn(),
            ],
            'oturum' => [
                'transaction_active' => $this->transactionActive,
                'autocommit' => $this->query('SELECT @@autocommit')->fetchColumn(),
            ]
        ];

        if ($detayli) {
            // Detaylı veritabanı istatistikleri
            try {
                // Tablo sayısı
                $tablolar = $this->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                $durum['istatistik']['tablo_sayisi'] = count($tablolar);
                $durum['istatistik']['tablolar'] = $tablolar;

                // Veritabanı boyutu
                $boyut = $this->query("
                    SELECT
                        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS boyut_mb
                    FROM information_schema.TABLES
                    WHERE table_schema = '{$this->veritabaniAdi}'
                ")->fetch(PDO::FETCH_OBJ);
                $durum['istatistik']['boyut_mb'] = $boyut->boyut_mb;

                // Aktif bağlantı sayısı
                $baglanti = $this->query("SHOW STATUS LIKE 'Threads_connected'")->fetch(PDO::FETCH_OBJ);
                $durum['istatistik']['aktif_baglanti'] = $baglanti->Value ?? 0;

                // Sorgu cache durumu
                $cache = $this->query("SHOW VARIABLES LIKE 'query_cache_type'")->fetch(PDO::FETCH_OBJ);
                $durum['istatistik']['query_cache'] = $cache->Value ?? 'OFF';

                // Max bağlantı limiti
                $maxConn = $this->query("SHOW VARIABLES LIKE 'max_connections'")->fetch(PDO::FETCH_OBJ);
                $durum['istatistik']['max_baglanti'] = $maxConn->Value ?? 0;

                // Uptime
                $uptime = $this->query("SHOW STATUS LIKE 'Uptime'")->fetch(PDO::FETCH_OBJ);
                $uptimeSaniye = $uptime->Value ?? 0;
                $durum['istatistik']['uptime'] = [
                    'saniye' => $uptimeSaniye,
                    'okunabilir' => $this->saniyeyiFormatla($uptimeSaniye)
                ];
            } catch (PDOException $e) {
                Log::Hata(0, "Veritabanı durum bilgisi alınamadı: " . $e->getMessage());
                $durum['istatistik']['hata'] = $e->getMessage();
            }
        }

        return $durum;
    }

    /**
     * Saniyeyi okunabilir formata çevirir
     * @param int $saniye
     * @return string
     */
    private function saniyeyiFormatla($saniye)
    {
        $gun = floor($saniye / 86400);
        $saat = floor(($saniye % 86400) / 3600);
        $dakika = floor(($saniye % 3600) / 60);
        $sn = $saniye % 60;

        $parts = [];
        if ($gun > 0) $parts[] = $gun . ' gün';
        if ($saat > 0) $parts[] = $saat . ' saat';
        if ($dakika > 0) $parts[] = $dakika . ' dakika';
        if ($sn > 0 || empty($parts)) $parts[] = $sn . ' saniye';

        return implode(', ', $parts);
    }

    /**
     * AND WHERE koşulu ekler
     * @param string $data Kolon adı
     * @param mixed $value Değer
     * @param string $operator Operatör
     * @return $this
     */
    public function andWhere($data, $value, $operator = '=')
    {
        $data = $this->filtrele($data);
        $operator = strtoupper($operator);
        $allowedOperators = ['=', '>', '<', '>=', '<=', '!=', '<>', 'LIKE'];

        if (!in_array($operator, $allowedOperators)) {
            Log::Hata(0, "andWhere metodunda geçersiz operatör kullanıldı: $operator");
            throw new Exception("Geçersiz operatör: $operator");
        }

        $paramName = $data . '_' . uniqid();
        $this->kod .= " AND $data $operator :$paramName";
        $this->whereParams[$paramName] = $value;
        return $this;
    }

    /**
     * OR WHERE koşulu ekler
     * @param string $data Kolon adı
     * @param mixed $value Değer
     * @param string $operator Operatör
     * @return $this
     */
    public function orWhere($data, $value, $operator = '=')
    {
        $data = $this->filtrele($data);
        $operator = strtoupper($operator);
        $allowedOperators = ['=', '>', '<', '>=', '<=', '!=', '<>', 'LIKE'];

        if (!in_array($operator, $allowedOperators)) {
            Log::Hata(0, "orWhere metodunda geçersiz operatör kullanıldı: $operator");
            throw new Exception("Geçersiz operatör: $operator");
        }

        $paramName = $data . '_' . uniqid();
        $this->kod .= " OR $data $operator :$paramName";
        $this->whereParams[$paramName] = $value;
        return $this;
    }

    /**
     * WHERE IN koşulu ekler
     * @param string $data Kolon adı
     * @param array $values Değerler dizisi
     * @return $this
     */
    public function whereIn($data, array $values)
    {
        if (empty($values)) {
            Log::Hata(0, "whereIn için değer dizisi boş olamaz");
            throw new Exception("whereIn için değer dizisi boş olamaz");
        }

        $data = $this->filtrele($data);
        $placeholders = [];

        foreach ($values as $index => $value) {
            $paramName = $data . '_in_' . $index . '_' . uniqid();
            $placeholders[] = ':' . $paramName;
            $this->whereParams[$paramName] = $value;
        }

        $this->kod .= " WHERE $data IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }

    /**
     * WHERE BETWEEN koşulu ekler
     * @param string $data Kolon adı
     * @param mixed $start Başlangıç değeri
     * @param mixed $end Bitiş değeri
     * @return $this
     */
    public function whereBetween($data, $start, $end)
    {
        $data = $this->filtrele($data);
        $paramStart = $data . '_start_' . uniqid();
        $paramEnd = $data . '_end_' . uniqid();

        $this->kod .= " WHERE $data BETWEEN :$paramStart AND :$paramEnd";
        $this->whereParams[$paramStart] = $start;
        $this->whereParams[$paramEnd] = $end;
        return $this;
    }

    /**
     * INNER JOIN ekler
     * @param string $tablo Birleştirilecek tablo
     * @param string $kolon1 İlk kolon
     * @param string $kolon2 İkinci kolon
     * @param string $operator Operatör (varsayılan =)
     * @return $this
     */
    public function join($tablo, $kolon1, $kolon2, $operator = '=')
    {
        $tablo = $this->filtrele($tablo);
        $kolon1 = $this->filtrele($kolon1);
        $kolon2 = $this->filtrele($kolon2);

        $this->kod .= " INNER JOIN $tablo ON $kolon1 $operator $kolon2";
        return $this;
    }

    /**
     * LEFT JOIN ekler
     * @param string $tablo Birleştirilecek tablo
     * @param string $kolon1 İlk kolon
     * @param string $kolon2 İkinci kolon
     * @param string $operator Operatör
     * @return $this
     */
    public function leftJoin($tablo, $kolon1, $kolon2, $operator = '=')
    {
        $tablo = $this->filtrele($tablo);
        $kolon1 = $this->filtrele($kolon1);
        $kolon2 = $this->filtrele($kolon2);

        $this->kod .= " LEFT JOIN $tablo ON $kolon1 $operator $kolon2";
        return $this;
    }

    /**
     * RIGHT JOIN ekler
     * @param string $tablo Birleştirilecek tablo
     * @param string $kolon1 İlk kolon
     * @param string $kolon2 İkinci kolon
     * @param string $operator Operatör
     * @return $this
     */
    public function rightJoin($tablo, $kolon1, $kolon2, $operator = '=')
    {
        $tablo = $this->filtrele($tablo);
        $kolon1 = $this->filtrele($kolon1);
        $kolon2 = $this->filtrele($kolon2);

        $this->kod .= " RIGHT JOIN $tablo ON $kolon1 $operator $kolon2";
        return $this;
    }

    /**
     * GROUP BY ekler
     * @param string $kolon Gruplamak için kolon
     * @return $this
     */
    public function groupBy($kolon)
    {
        $kolon = $this->filtrele($kolon);
        $this->kod .= " GROUP BY $kolon";
        return $this;
    }

    /**
     * HAVING koşulu ekler
     * @param string $kosul HAVING koşulu
     * @return $this
     */
    public function having($kosul)
    {
        $this->kod .= " HAVING $kosul";
        return $this;
    }

    /**
     * Belirli kolonları seçer
     * @param array|string $kolonlar Seçilecek kolonlar
     * @return $this
     */
    public function secKolon($kolonlar)
    {
        if (is_array($kolonlar)) {
            $kolonlar = array_map([$this, 'filtrele'], $kolonlar);
            $kolonStr = implode(', ', $kolonlar);
        } else {
            $kolonStr = $this->filtrele($kolonlar);
        }

        $this->kod = str_replace('SELECT *', 'SELECT ' . $kolonStr, $this->kod);
        return $this;
    }

    /**
     * Transaction başlatır
     * @return bool
     */
    public function transactionBaslat()
    {
        if (!$this->transactionActive) {
            $result = $this->beginTransaction();
            $this->transactionActive = true;
            Log::Bilgi(0, "Transaction baslatildi", GELISTIRICI);
            return $result;
        }
        return false;
    }

    /**
     * Transaction'ı commit eder
     * @return bool
     */
    public function transactionKaydet()
    {
        if ($this->transactionActive) {
            $result = $this->commit();
            $this->transactionActive = false;
            Log::Basari(0, "Transaction kaydedildi", GELISTIRICI);
            return $result;
        }
        return false;
    }

    /**
     * Transaction'ı geri alır
     * @return bool
     */
    public function transactionGeriAl()
    {
        if ($this->transactionActive) {
            $result = $this->rollBack();
            $this->transactionActive = false;
            Log::Dikkat(0, "Transaction geri alindi", GELISTIRICI);
            return $result;
        }
        return false;
    }

    /**
     * Son eklenen kaydın ID'sini döndürür
     * @return string
     */
    public function sonEklenenId()
    {
        return $this->lastInsertId();
    }

    /**
     * OFFSET ekler (pagination için)
     * @param int $offset Atlanacak kayıt sayısı
     * @return $this
     */
    public function offset($offset)
    {
        $this->kod .= ' OFFSET ' . (int)$offset;
        return $this;
    }

    /**
     * Sorguyu sıfırlar
     * @return $this
     */
    public function sifirla()
    {
        $this->kod = '';
        $this->kod2 = null;
        $this->whereParams = [];
        $this->tabloAdi = '';
        return $this;
    }

    /**
     * WHERE NULL koşulu ekler
     * @param string $kolon Kolon adı
     * @return $this
     */
    public function whereNull($kolon)
    {
        $kolon = $this->filtrele($kolon);
        $this->kod .= " WHERE $kolon IS NULL";
        return $this;
    }

    /**
     * WHERE NOT NULL koşulu ekler
     * @param string $kolon Kolon adı
     * @return $this
     */
    public function whereNotNull($kolon)
    {
        $kolon = $this->filtrele($kolon);
        $this->kod .= " WHERE $kolon IS NOT NULL";
        return $this;
    }

    /**
     * WHERE NOT IN koşulu ekler
     * @param string $data Kolon adı
     * @param array $values Değerler dizisi
     * @return $this
     */
    public function whereNotIn($data, array $values)
    {
        if (empty($values)) {
            Log::Hata(0, "whereNotIn için değer dizisi boş olamaz");
            throw new Exception("whereNotIn için değer dizisi boş olamaz");
        }

        $data = $this->filtrele($data);
        $placeholders = [];

        foreach ($values as $index => $value) {
            $paramName = $data . '_notin_' . $index . '_' . uniqid();
            $placeholders[] = ':' . $paramName;
            $this->whereParams[$paramName] = $value;
        }

        $this->kod .= " WHERE $data NOT IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }

    /**
     * DISTINCT seçimi ekler
     * @return $this
     */
    public function distinct()
    {
        $this->kod = str_replace('SELECT', 'SELECT DISTINCT', $this->kod);
        return $this;
    }

    /**
     * Kayıt var mı kontrol eder
     * @return bool
     */
    public function exists()
    {
        $this->kod .= " LIMIT 1";
        $this->kod = str_replace('SELECT *', 'SELECT 1', $this->kod);

        try {
            $allParams = array_merge(
                (array)$this->kod2,
                $this->whereParams
            );
            $sorgu = $this->prepare($this->kod);
            $sorgu->execute($allParams);
            $this->whereParams = [];
            return $sorgu->rowCount() > 0;
        } catch (PDOException $e) {
            Log::Hata(0, "Exists kontrolü hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * İlk kaydı döndürür (tekli fetch için kısayol)
     * @return object|null
     */
    public function first()
    {
        $this->limit(1);
        return $this->calistir('tekli');
    }

    /**
     * Tüm kayıtları döndürür (çoklu fetch için kısayol)
     * @return array
     */
    public function get()
    {
        return $this->calistir('coklu');
    }

    /**
     * Tek bir kolon değerlerini array olarak döndürür
     * @param string $kolon Kolon adı
     * @return array
     */
    public function pluck($kolon)
    {
        $kolon = $this->filtrele($kolon);
        $this->secKolon($kolon);

        try {
            $allParams = array_merge(
                (array)$this->kod2,
                $this->whereParams
            );
            $sorgu = $this->prepare($this->kod);
            $sorgu->execute($allParams);
            $this->whereParams = [];
            return $sorgu->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            Log::Hata(0, "Pluck hatası: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sayfalama desteği
     * @param int $sayfa Sayfa numarası (1'den başlar)
     * @param int $adet Sayfa başına kayıt sayısı
     * @return array
     */
    public function paginate($sayfa = 1, $adet = 15)
    {
        $sayfa = max(1, (int)$sayfa);
        $adet = max(1, (int)$adet);
        $offset = ($sayfa - 1) * $adet;

        // Toplam kayıt sayısını al
        $countKod = str_replace('SELECT *', 'SELECT COUNT(*) as total', $this->kod);

        try {
            $allParams = array_merge(
                (array)$this->kod2,
                $this->whereParams
            );

            $countSorgu = $this->prepare($countKod);
            $countSorgu->execute($allParams);
            $toplam = $countSorgu->fetch(PDO::FETCH_OBJ)->total;

            // Sayfa verilerini al
            $this->limit($adet)->offset($offset);
            $veriler = $this->get();

            return [
                'veriler' => $veriler,
                'sayfa' => $sayfa,
                'sayfa_basina' => $adet,
                'toplam_kayit' => $toplam,
                'toplam_sayfa' => ceil($toplam / $adet),
                'onceki_sayfa' => $sayfa > 1 ? $sayfa - 1 : null,
                'sonraki_sayfa' => $sayfa < ceil($toplam / $adet) ? $sayfa + 1 : null
            ];
        } catch (PDOException $e) {
            Log::Hata(0, "Paginate hatası: " . $e->getMessage());
            return [
                'veriler' => [],
                'sayfa' => $sayfa,
                'sayfa_basina' => $adet,
                'toplam_kayit' => 0,
                'toplam_sayfa' => 0,
                'onceki_sayfa' => null,
                'sonraki_sayfa' => null
            ];
        }
    }

    /**
     * Kolon değerini artırır
     * @param string $kolon Kolon adı
     * @param int $miktar Artırılacak miktar
     * @return $this
     */
    public function increment($kolon, $miktar = 1)
    {
        $kolon = $this->filtrele($kolon);
        $miktar = (int)$miktar;
        $this->kod .= " SET $kolon = $kolon + $miktar";
        return $this->calistir();
    }

    /**
     * Kolon değerini azaltır
     * @param string $kolon Kolon adı
     * @param int $miktar Azaltılacak miktar
     * @return $this
     */
    public function decrement($kolon, $miktar = 1)
    {
        $kolon = $this->filtrele($kolon);
        $miktar = (int)$miktar;
        $this->kod .= " SET $kolon = $kolon - $miktar";
        return $this->calistir();
    }

    /**
     * Aggregate fonksiyonları
     * @param string $fonksiyon COUNT, MAX, MIN, AVG, SUM
     * @param string $kolon Kolon adı (COUNT için opsiyonel)
     * @return mixed
     */
    public function aggregate($fonksiyon, $kolon = '*')
    {
        $fonksiyon = strtoupper($fonksiyon);
        $allowedFunctions = ['COUNT', 'MAX', 'MIN', 'AVG', 'SUM'];

        if (!in_array($fonksiyon, $allowedFunctions)) {
            Log::Hata(0, "Geçersiz aggregate fonksiyonu: $fonksiyon");
            throw new Exception("Geçersiz aggregate fonksiyonu: $fonksiyon");
        }

        if ($kolon !== '*') {
            $kolon = $this->filtrele($kolon);
        }

        $this->kod = str_replace('SELECT *', "SELECT $fonksiyon($kolon) as sonuc", $this->kod);

        try {
            $allParams = array_merge(
                (array)$this->kod2,
                $this->whereParams
            );
            $sorgu = $this->prepare($this->kod);
            $sorgu->execute($allParams);
            $this->whereParams = [];
            return $sorgu->fetch(PDO::FETCH_OBJ)->sonuc;
        } catch (PDOException $e) {
            Log::Hata(0, "Aggregate hatası: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Maksimum değeri döndürür
     * @param string $kolon
     * @return mixed
     */
    public function max($kolon)
    {
        return $this->aggregate('MAX', $kolon);
    }

    /**
     * Minimum değeri döndürür
     * @param string $kolon
     * @return mixed
     */
    public function min($kolon)
    {
        return $this->aggregate('MIN', $kolon);
    }

    /**
     * Ortalama değeri döndürür
     * @param string $kolon
     * @return mixed
     */
    public function avg($kolon)
    {
        return $this->aggregate('AVG', $kolon);
    }

    /**
     * Toplam değeri döndürür
     * @param string $kolon
     * @return mixed
     */
    public function sum($kolon)
    {
        return $this->aggregate('SUM', $kolon);
    }

    /**
     * Toplam kayıt sayısını döndürür
     * @param string $kolon
     * @return int
     */
    public function count($kolon = '*')
    {
        return (int)$this->aggregate('COUNT', $kolon);
    }

    /**
     * Tarih filtreleme (gün için)
     * @param string $kolon
     * @param string $tarih (YYYY-MM-DD formatında)
     * @return $this
     */
    public function whereDate($kolon, $tarih)
    {
        $kolon = $this->filtrele($kolon);
        $paramName = $kolon . '_date_' . uniqid();
        $this->kod .= " WHERE DATE($kolon) = :$paramName";
        $this->whereParams[$paramName] = $tarih;
        return $this;
    }

    /**
     * Ay filtreleme
     * @param string $kolon
     * @param int $ay (1-12)
     * @return $this
     */
    public function whereMonth($kolon, $ay)
    {
        $kolon = $this->filtrele($kolon);
        $ay = (int)$ay;
        $this->kod .= " WHERE MONTH($kolon) = $ay";
        return $this;
    }

    /**
     * Yıl filtreleme
     * @param string $kolon
     * @param int $yil
     * @return $this
     */
    public function whereYear($kolon, $yil)
    {
        $kolon = $this->filtrele($kolon);
        $yil = (int)$yil;
        $this->kod .= " WHERE YEAR($kolon) = $yil";
        return $this;
    }

    /**
     * Sorgu performans analizi (EXPLAIN)
     * @return array
     */
    public function explain()
    {
        try {
            $explainKod = "EXPLAIN " . $this->kod;
            $allParams = array_merge(
                (array)$this->kod2,
                $this->whereParams
            );
            $sorgu = $this->prepare($explainKod);
            $sorgu->execute($allParams);
            $this->whereParams = [];
            return $sorgu->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Log::Hata(0, "Explain hatası: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tablo yapısını döndürür
     * @param string $tablo
     * @return array
     */
    public function tabloYapisi($tablo)
    {
        $tablo = $this->filtrele($tablo);
        try {
            $sorgu = $this->query("DESCRIBE $tablo");
            return $sorgu->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Log::Hata(0, "Tablo yapısı alma hatası: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Index oluşturur
     * @param string $tablo
     * @param string $indexAdi
     * @param array $kolonlar
     * @param bool $unique
     * @return bool
     */
    public function indexOlustur($tablo, $indexAdi, array $kolonlar, $unique = false)
    {
        $tablo = $this->filtrele($tablo);
        $indexAdi = $this->filtrele($indexAdi);
        $kolonlar = array_map([$this, 'filtrele'], $kolonlar);

        $tip = $unique ? 'UNIQUE INDEX' : 'INDEX';
        $kolonStr = implode(', ', $kolonlar);
        $sql = "CREATE $tip $indexAdi ON $tablo ($kolonStr)";

        try {
            $this->exec($sql);
            Log::Basari(0, "Index oluşturuldu: $indexAdi", GELISTIRICI);
            return true;
        } catch (PDOException $e) {
            Log::Hata(0, "Index oluşturma hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Index siler
     * @param string $tablo
     * @param string $indexAdi
     * @return bool
     */
    public function indexSil($tablo, $indexAdi)
    {
        $tablo = $this->filtrele($tablo);
        $indexAdi = $this->filtrele($indexAdi);

        try {
            $this->exec("DROP INDEX $indexAdi ON $tablo");
            Log::Basari(0, "Index silindi: $indexAdi", GELISTIRICI);
            return true;
        } catch (PDOException $e) {
            Log::Hata(0, "Index silme hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tablo yedeği alır (SQL dump)
     * @param string $tablo
     * @return string|bool Dosya yolu veya false
     */
    public function tabloYedekle($tablo)
    {
        $tablo = $this->filtrele($tablo);

        // Yedekleme klasörünü belirle
        $yedekKlasoru = defined('KLASOR_DATABASE') ? KLASOR_DATABASE : (UYGULAMA . 'Database' . DIRECTORY_SEPARATOR);

        // Klasör yoksa oluştur
        if (!is_dir($yedekKlasoru) && !mkdir($yedekKlasoru, 0755, true)) {
            Log::Hata(0, "Yedekleme klasörü oluşturulamadı: $yedekKlasoru");
            return false;
        }

        $dosyaYolu = $yedekKlasoru . $tablo . '_' . date('Y-m-d_H-i-s') . '.sql';

        try {
            // İşlem süresini ölç
            $baslangicZamani = microtime(true);

            // Tablo yapısını al
            $createTable = $this->query("SHOW CREATE TABLE $tablo")->fetch(PDO::FETCH_ASSOC);

            // Kayıt sayısını önceden al
            $veriler = $this->query("SELECT * FROM $tablo")->fetchAll(PDO::FETCH_ASSOC);
            $kayitSayisi = count($veriler);

            // Tablo boyutunu al
            $tabloBoyut = $this->query("
                SELECT
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS boyut_mb,
                    ROUND((data_length / 1024 / 1024), 2) AS veri_mb,
                    ROUND((index_length / 1024 / 1024), 2) AS index_mb
                FROM information_schema.TABLES
                WHERE table_schema = '{$this->veritabaniAdi}'
                AND table_name = '$tablo'
            ")->fetch(PDO::FETCH_OBJ);

            // Header bilgileri
            $sql = "-- ============================================\n";
            $sql .= "-- Tablo Yedeği: $tablo\n";
            $sql .= "-- Tarih: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Veritabanı: {$this->veritabaniAdi}\n";
            $sql .= "-- Toplam Kayıt: $kayitSayisi\n";
            $sql .= "-- Tablo Boyutu: " . ($tabloBoyut->boyut_mb ?? 0) . " MB (Veri: " . ($tabloBoyut->veri_mb ?? 0) . " MB, Index: " . ($tabloBoyut->index_mb ?? 0) . " MB)\n";
            $sql .= "-- Karakter Seti: UTF-8\n";
            $sql .= "-- PHP Versiyon: " . PHP_VERSION . "\n";
            $sql .= "-- MySQL Versiyon: " . $this->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";

            // Tema bilgileri varsa ekle
            if (defined('TEMA_BASLIK')) {
                $sql .= "-- --------------------------------------------\n";
                $sql .= "-- Proje: " . TEMA_BASLIK . "\n";
            }
            if (defined('TEMA_ACIKLAMA')) {
                $sql .= "-- Açıklama: " . TEMA_ACIKLAMA . "\n";
            }
            if (defined('TEMA_VERSIYON')) {
                $sql .= "-- Versiyon: " . TEMA_VERSIYON . "\n";
            }
            if (defined('TEMA_SAHIP_AD')) {
                $sql .= "-- Yedekleyen: " . TEMA_SAHIP_AD . "\n";
            }
            if (defined('TEMA_SAHIP_EMAIL')) {
                $sql .= "-- Email: " . TEMA_SAHIP_EMAIL . "\n";
            }
            if (defined('TEMA_SAHIP_URL')) {
                $sql .= "-- URL: " . TEMA_SAHIP_URL . "\n";
            }

            $sql .= "-- ============================================\n\n";
            $sql .= "SET NAMES utf8;\n";
            $sql .= "SET time_zone = '+00:00';\n";
            $sql .= "SET foreign_key_checks = 0;\n";
            $sql .= "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

            $sql .= "DROP TABLE IF EXISTS `$tablo`;\n\n";
            $sql .= $createTable['Create Table'] . ";\n\n";

            // Verileri al
            $veriler = $this->query("SELECT * FROM $tablo")->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($veriler)) {
                $sql .= "-- Veri ekleme\n";
                foreach ($veriler as $veri) {
                    $kolonlar = '`' . implode('`, `', array_keys($veri)) . '`';
                    $degerler = implode(', ', array_map(function ($v) {
                        return $v === null ? 'NULL' : $this->quote($v);
                    }, array_values($veri)));

                    $sql .= "INSERT INTO `$tablo` ($kolonlar) VALUES ($degerler);\n";
                }
            } else {
                $sql .= "-- Tabloda veri yok\n";
            }

            // İşlem süresini hesapla
            $bitisZamani = microtime(true);
            $islemSuresi = round($bitisZamani - $baslangicZamani, 3);

            if (file_put_contents($dosyaYolu, $sql) !== false) {
                $boyut = filesize($dosyaYolu);
                $boyutKB = round($boyut / 1024, 2);

                Log::Basari(0, "Tablo yedeklendi: $dosyaYolu | Boyut: {$boyutKB}KB | Kayıt: " . count($veriler) . " | Süre: {$islemSuresi}s", GELISTIRICI);
                return $dosyaYolu;
            } else {
                Log::Hata(0, "Yedekleme dosyası yazılamadı: $dosyaYolu");
                return false;
            }
        } catch (Exception $e) {
            Log::Hata(0, "Yedekleme hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ham SQL sorgusu çalıştırır (dikkatli kullanın!)
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function rawQuery($sql, array $params = [])
    {
        try {
            Log::Bilgi(0, "Raw SQL: $sql | Params: " . json_encode($params), GELISTIRICI);
            $sorgu = $this->prepare($sql);
            $sorgu->execute($params);

            // SELECT sorgusu mu?
            if (stripos(trim($sql), 'SELECT') === 0) {
                return $sorgu->fetchAll(PDO::FETCH_OBJ);
            }

            return $sorgu->rowCount();
        } catch (PDOException $e) {
            Log::Hata(0, "Raw query hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Şu anki SQL kodunu döndürür (debug için)
     * @return string
     */
    public function getSql()
    {
        return $this->kod;
    }

    /**
     * Parametreleri döndürür (debug için)
     * @return array
     */
    public function getParams()
    {
        return array_merge(
            (array)$this->kod2,
            $this->whereParams
        );
    }
}
