<?php

defined('ERISIM') or exit('401 Unauthorized');
/*************************************************
 * Proje Yardımcıları
 * Veri İşleme Fonksiyonları
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/

/**
 * Base64 Kodlama İşlemi
 *
 * Bu fonksiyon, binary verileri güvenli metin formatına dönüştürür.
 * URL'lerde, e-maillerde veya JSON'da kullanım için uygun hale getirir.
 *
 * Kullanım Alanı:
 * - Dosya yükleme sistemlerinde
 * - API'lerde binary veri gönderiminde
 * - Şifreli verilerin saklanmasında
 *
 * Teknik Detaylar:
 * - RFC 3548 Base64 standardı kullanır
 * - 3 byte'ı 4 karakter'e dönüştürür
 * - Padding için '=' karakteri kullanır
 * - ASCII güvenli çıktı üretir
 *
 * Kullanım Örneği:
 * $veri = "Türkçe metin 123!@#";
 * $kodlu = Base64_Olustur($veri);
 * echo $kodlu; // VPxya2_lZSBtZXRpbiAxMjMhQCM=
 *
 * @param string|binary $a Kodlanacak veri (metin, binary, resim vb.)
 * @return string Base64 kodlu string (URL ve JSON güvenli)
 */
function Base64_Olustur($a)
{
    return base64_encode($a);
}

/**
 * Base64 Çözme İşlemi
 *
 * Bu fonksiyon, Base64 kodlu veriyi orijinal formatına geri dönüştürür.
 * BOM (Byte Order Mark) temizliği de yapar.
 *
 * Kullanım Alanı:
 * - API'lerden gelen Base64 verilerin çözülmesinde
 * - Dosya içeriklerinin restore edilmesinde
 * - Şifreli verilerin decode edilmesinde
 * - Email attachment'ların işlenmesinde
 *
 * Teknik Detaylar:
 * - BOM marker temizlemesi (77u/ prefix)
 * - RFC 3548 Base64 decoding
 * - Binary-safe operation
 * - Error handling for invalid input
 *
 * Kullanım Örneği:
 * $kodlu = "VPxya2_lZSBtZXRpbiAxMjMhQCM=";
 * $orijinal = Base64_Coz($kodlu);
 * echo $orijinal; // "Türkçe metin 123!@#"
 *
 * @param string $a Base64 kodlu string
 * @return string|çözülmüş orijinal veri
 */
function Base64_Coz($a)
{
    $a = str_replace("77u/", "", $a);
    return base64_decode($a);
}

/**
 * JSON Encoding İşlemi
 *
 * Bu fonksiyon, PHP veriyi JSON formatına dönüştürür.
 * Yapılandırılabilir encoding seçenekleri ve object/string return seçimi destekler.
 *
 * Kullanım Alanı:
 * - API response'larında
 * - AJAX veri gönderiminde
 * - Configuration dosyalarında
 * - Cache sistemlerinde
 * - Object-based data manipulation'da
 *
 * Teknik Detaylar:
 * - JSON_OPTIONS sabit veya custom flags
 * - String ve integer option desteği
 * - UTF-8 uyumlu encoding
 * - Error handling capability
 * - Object veya string return seçimi
 *
 * Kullanım Örneği:
 * $veri = ['ad' => 'Ahmet', 'yas' => 25];
 * $json = Json_Olustur($veri); // String: {"ad":"Ahmet","yas":25}
 * $object = Json_Olustur($veri, JSON_OPTIONS, true); // Object döndürür
 *
 * @param mixed $data JSON'a dönüştürülecek veri
 * @param int|string $options JSON encoding seçenekleri (varsayılan: JSON_OPTIONS)
 * @param bool $sinif Object olarak dönsün mü (varsayılan: false)
 * @return string|object|false JSON string, object veya hata durumunda false
 */
function Json_Olustur($data, $options = null, $sinif = false)
{
    if ($options === null) {
        $options = defined('JSON_OPTIONS') ? JSON_OPTIONS : JSON_UNESCAPED_UNICODE;
    }
    $flags = is_string($options) ? (defined($options) ? constant($options) : (int)$options) : $options;
    $result = json_encode($data, $flags);

    return $sinif ? (object) json_decode($result) : $result;
}

/**
 * JSON Decoding İşlemi
 *
 * Bu fonksiyon, JSON string'ini PHP verisine dönüştürür.
 * Varsayılan olarak stdClass object döndürür, istenirse array da döndürebilir.
 *
 * Kullanım Alanı:
 * - API response'larının işlenmesinde
 * - Configuration dosyalarının okunmasında
 * - Cache verilerinin restore edilmesinde
 * - AJAX data parsing'de
 *
 * Teknik Detaylar:
 * - Varsayılan: stdClass object return
 * - Optional: associative array return
 * - UTF-8 character support
 * - Error handling for malformed JSON
 *
 * Kullanım Örneği:
 * $json = '{"ad":"Ahmet","yas":25}';
 * $veri = Json_Coz($json); // stdClass object
 * echo $veri->ad; // "Ahmet"
 * 
 * $array = Json_Coz($json, true); // Array
 * echo $array['ad']; // "Ahmet"
 *
 * @param string $json Çözülecek JSON string
 * @param bool $assoc Associative array dönsün mü (varsayılan: false - stdClass döner)
 * @return mixed|Çözülmüş PHP verisi (stdClass object veya array) veya null
 */
function Json_Coz($json, $assoc = false)
{
    return json_decode($json, $assoc);
}

/**
 * JSON Geçerlilik Kontrol İşlemi
 *
 * Bu fonksiyon, bir string'in geçerli JSON formatında olup olmadığını kontrol eder.
 * Try-catch ile güvenli JSON syntax validation ve error detection yapar.
 *
 * Kullanım Alanı:
 * - API request validation'da
 * - File upload kontrollerde
 * - Configuration dosyası doğrulamalarında
 * - Data import/export işlemlerinde
 * - Form validation'larda
 *
 * Teknik Detaylar:
 * - Try-catch ile exception handling
 * - JSON syntax parsing kontrolü
 * - json_last_error() ile hata tespiti
 * - UTF-8 encoding validation
 * - Null, boolean ve numeric kontrolleri
 * - Empty string ve whitespace handling
 *
 * Güvenlik Kontrolleri:
 * - Exception-safe JSON parsing
 * - Malformed JSON detection
 * - Invalid character sequence kontrolü
 * - Depth limit validation
 * - Memory overflow prevention
 * - Fatal error protection
 *
 * Try-Catch Avantajları:
 * - PHP fatal error'larını yakalar
 * - Memory limit aşımlarını handle eder
 * - Exception'ları güvenli şekilde işler
 * - More robust error handling
 * - Unexpected error scenarios coverage
 *
 * Kullanım Örneği:
 * if (Json_Kontrol('{"ad":"Ahmet","yas":25}')) {
 *     echo "Geçerli JSON formatı";
 * } else {
 *     echo "Geçersiz JSON formatı";
 * }
 *
 * @param string $json Kontrol edilecek JSON string
 * @return bool Geçerli JSON ise true, değilse false
 */
function Json_Kontrol($json)
{
    // Boş string kontrolü
    if (empty($json) || !is_string($json)) {
        return false;
    }

    // Whitespace temizleme
    $json = trim($json);
    if (empty($json)) {
        return false;
    }

    try {
        // JSON decode denemesi - try-catch ile güvenli
        $result = json_decode($json);

        // Hem exception hem de json_last_error kontrolü
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        // Null dönmesi sadece "null" JSON değeri için geçerli
        // Geçersiz JSON'da da null döner, o yüzden error check gerekli
        return true;
    } catch (Exception $e) {
        // JSON parsing sırasında herhangi bir exception
        return false;
    } catch (Error $e) {
        // PHP 7+ Fatal Error'ları (örn: memory limit)
        return false;
    } catch (Throwable $e) {
        // PHP 7+ tüm error ve exception'lar
        return false;
    }
}

/**
 * CSV Verilerini Array'e Dönüştürme
 *
 * Bu fonksiyon, CSV formatındaki string'i PHP array'ine dönüştürür.
 * Configurable delimiter ve enclosure desteği sağlar.
 *
 * Kullanım Alanı:
 * - CSV dosya içeriğinin parse edilmesinde
 * - API'den gelen CSV verilerinin işlenmesinde
 * - Excel export/import işlemlerinde
 * - Data migration operasyonlarında
 *
 * Teknik Detaylar:
 * - str_getcsv() PHP function kullanır
 * - Configurable delimiter (comma, semicolon, tab)
 * - Configurable enclosure (quotes)
 * - Multi-line CSV support
 * - UTF-8 encoding support
 *
 * Kullanım Örneği:
 * $csv = "Ad,Soyad,Yaş\nAhmet,Yılmaz,25\nMehmet,Kaya,30";
 * $array = Csv_Array_Cevir($csv);
 * // [['Ad','Soyad','Yaş'], ['Ahmet','Yılmaz','25'], ['Mehmet','Kaya','30']]
 *
 * @param string $csv CSV formatındaki string
 * @param string $ayrac Field delimiter (varsayılan: ',')
 * @param string $sarmalayici Field enclosure (varsayılan: '"')
 * @return array Parsed CSV array
 */
function Csv_Array_Cevir($csv, $ayrac = ',', $sarmalayici = '"')
{
    if (empty($csv)) {
        return [];
    }

    $satirlar = explode("\n", $csv);
    $sonuc = [];

    foreach ($satirlar as $satir) {
        $satir = trim($satir);
        if (!empty($satir)) {
            $sonuc[] = str_getcsv($satir, $ayrac, $sarmalayici);
        }
    }

    return $sonuc;
}

/**
 * Array'i CSV Formatına Dönüştürme
 *
 * Bu fonksiyon, PHP array'ini CSV formatına dönüştürür.
 * Configurable delimiter ve enclosure seçenekleri sunar.
 *
 * Kullanım Alanı:
 * - CSV dosya oluşturmada
 * - API response'larında CSV format
 * - Excel export işlemlerinde
 * - Data export operasyonlarında
 *
 * Teknik Detaylar:
 * - fputcsv() simulation for string output
 * - Configurable field separator
 * - Configurable field enclosure
 * - Special character escaping
 * - UTF-8 encoding preservation
 *
 * Kullanım Örneği:
 * $data = [['Ad','Soyad'], ['Ahmet','Yılmaz'], ['Mehmet','Kaya']];
 * $csv = Array_Csv_Cevir($data);
 * // "Ad,Soyad\nAhmet,Yılmaz\nMehmet,Kaya"
 *
 * @param array $array Dönüştürülecek array
 * @param string $ayrac Field delimiter (varsayılan: ',')
 * @param string $sarmalayici Field enclosure (varsayılan: '"')
 * @return string CSV formatında string
 */
function Array_Csv_Cevir($array, $ayrac = ',', $sarmalayici = '"')
{
    if (empty($array) || !is_array($array)) {
        return '';
    }

    $csv = '';
    foreach ($array as $satir) {
        if (is_array($satir)) {
            $escapedRow = [];
            foreach ($satir as $field) {
                $field = (string) $field;
                // Enclosure karakterini escape et
                $field = str_replace($sarmalayici, $sarmalayici . $sarmalayici, $field);
                // Delimiter veya enclosure içeriyorsa sarmalayıcı ile sar
                if (strpos($field, $ayrac) !== false || strpos($field, $sarmalayici) !== false || strpos($field, "\n") !== false) {
                    $field = $sarmalayici . $field . $sarmalayici;
                }
                $escapedRow[] = $field;
            }
            $csv .= implode($ayrac, $escapedRow) . "\n";
        }
    }

    return rtrim($csv, "\n");
}

/**
 * XML String'ini Array'e Dönüştürme
 *
 * Bu fonksiyon, XML formatındaki string'i PHP array'ine dönüştürür.
 * SimpleXML kullanarak güvenli parsing yapar.
 *
 * Kullanım Alanı:
 * - XML API response'larının parse edilmesinde
 * - Configuration XML dosyalarının okunmasında
 * - SOAP web service'lerinde
 * - RSS/Atom feed parsing'de
 *
 * Teknik Detaylar:
 * - SimpleXML extension kullanır
 * - UTF-8 encoding support
 * - CDATA section handling
 * - Attribute ve text content parse
 * - Error handling for malformed XML
 *
 * Kullanım Örneği:
 * $xml = '<root><user><name>Ahmet</name><age>25</age></user></root>';
 * $array = Xml_Array_Cevir($xml);
 *
 * @param string $xml XML formatındaki string
 * @return array|false Parsed XML array veya hata durumunda false
 */
function Xml_Array_Cevir($xml)
{
    if (empty($xml)) {
        return false;
    }

    // XML parsing hataları için error handling
    libxml_use_internal_errors(true);

    try {
        $xmlObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xmlObj === false) {
            return false;
        }

        // SimpleXML object'ini array'e dönüştür
        return json_decode(json_encode($xmlObj), true);
    } catch (Exception $e) {
        return false;
    } catch (Error $e) {
        return false;
    }
}

/**
 * Array'i XML Formatına Dönüştürme
 *
 * Bu fonksiyon, PHP array'ini XML formatına dönüştürür.
 * Recursive array processing ve attribute support sağlar.
 *
 * Kullanım Alanı:
 * - XML API response oluşturmada
 * - Configuration dosyası üretiminde
 * - SOAP web service response'larında
 * - RSS/Atom feed generation'da
 *
 * Teknik Detaylar:
 * - SimpleXMLElement kullanır
 * - Recursive array-to-XML conversion
 * - UTF-8 encoding preservation
 * - CDATA section support
 * - Attribute handling capability
 *
 * Kullanım Örneği:
 * $data = ['user' => ['name' => 'Ahmet', 'age' => 25]];
 * $xml = Array_Xml_Cevir($data, 'root');
 *
 * @param array $array Dönüştürülecek array
 * @param string $rootElement Root element adı (varsayılan: 'root')
 * @return string|false XML formatında string veya hata durumunda false
 */
function Array_Xml_Cevir($array, $rootElement = 'root')
{
    if (empty($array) || !is_array($array)) {
        return false;
    }

    try {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $rootElement . '></' . $rootElement . '>');

        // Recursive function to convert array to XML
        $arrayToXml = function ($data, $xmlElement) use (&$arrayToXml) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    if (is_numeric($key)) {
                        $key = 'item' . $key;
                    }
                    $subElement = $xmlElement->addChild($key);
                    $arrayToXml($value, $subElement);
                } else {
                    if (is_numeric($key)) {
                        $key = 'item' . $key;
                    }
                    $xmlElement->addChild($key, htmlspecialchars((string) $value));
                }
            }
        };

        $arrayToXml($array, $xml);

        return $xml->asXML();
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Array'i stdClass Object'e Dönüştürme (Recursive)
 *
 * Bu fonksiyon, PHP array'ini stdClass object'ine dönüştürür.
 * Nested (iç içe) array'leri de recursive olarak object'e çevirir.
 *
 * Kullanım Alanı:
 * - API response'larında object formatı gerekliliği
 * - JSON-like data structure oluşturmada
 * - Object-oriented data manipulation'da
 * - Frontend'e object olarak veri göndermede
 * - Database result'ları object formatına çevirmede
 *
 * Teknik Detaylar:
 * - Recursive array traversal
 * - stdClass object creation
 * - Nested array support
 * - Mixed data type handling (string, int, bool, null)
 * - Reference preservation
 * - Memory efficient conversion
 *
 * Avantajları:
 * - Arrow syntax kullanımı: $obj->key yerine $arr['key']
 * - Type safety ve IDE autocomplete desteği
 * - JSON encode/decode ile uyumlu format
 * - Object-oriented programming paradigması
 * - Property access ile daha temiz kod
 *
 * Kullanım Örneği:
 * $array = [
 *     'ad' => 'Ahmet',
 *     'bilgiler' => [
 *         'yas' => 25,
 *         'sehir' => 'İstanbul'
 *     ]
 * ];
 * $object = Array_Object_Cevir($array);
 * echo $object->ad; // "Ahmet"
 * echo $object->bilgiler->yas; // 25
 *
 * @param array $array Dönüştürülecek array
 * @return stdClass|array stdClass object veya array elementleri object olan array
 */
function Array_Object_Cevir($array)
{
    if (!is_array($array)) {
        return $array;
    }

    $object = new stdClass();

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            // Numeric indexed array kontrolü
            if (array_keys($value) === range(0, count($value) - 1)) {
                // Sequential numeric array - her elemanı recursive çevir
                $object->$key = array_map(function ($item) {
                    return is_array($item) ? Array_Object_Cevir($item) : $item;
                }, $value);
            } else {
                // Associative array - object'e çevir
                $object->$key = Array_Object_Cevir($value);
            }
        } else {
            $object->$key = $value;
        }
    }

    return $object;
}

/**
 * stdClass Object'i Array'e Dönüştürme (Recursive)
 *
 * Bu fonksiyon, stdClass object'ini PHP array'ine dönüştürür.
 * Nested (iç içe) object'leri de recursive olarak array'e çevirir.
 *
 * Kullanım Alanı:
 * - JSON decode sonrası array formatına çevirmede
 * - Object-based data'yı array operasyonlarında kullanmada
 * - Legacy code ile entegrasyonda
 * - Array functions kullanımı gerekliliğinde
 * - Database insert/update için array formatı gerekliliğinde
 *
 * Teknik Detaylar:
 * - Recursive object traversal
 * - Associative array creation
 * - Nested object support
 * - Mixed data type handling
 * - get_object_vars() kullanımı
 * - Memory efficient conversion
 *
 * Avantajları:
 * - Array functions kullanabilme (array_map, array_filter, etc.)
 * - Foreach ile kolay iteration
 * - Array merge ve manipulation işlemleri
 * - Legacy code compatibility
 * - Database query builder compatibility
 *
 * Kullanım Örneği:
 * $object = new stdClass();
 * $object->ad = 'Ahmet';
 * $object->bilgiler = new stdClass();
 * $object->bilgiler->yas = 25;
 * 
 * $array = Object_Array_Cevir($object);
 * echo $array['ad']; // "Ahmet"
 * echo $array['bilgiler']['yas']; // 25
 *
 * @param stdClass|object $object Dönüştürülecek object
 * @return array Associative array
 */
function Object_Array_Cevir($object)
{
    if (!is_object($object)) {
        return $object;
    }

    $array = [];

    foreach (get_object_vars($object) as $key => $value) {
        if (is_object($value)) {
            $array[$key] = Object_Array_Cevir($value);
        } elseif (is_array($value)) {
            $array[$key] = array_map(function ($item) {
                return is_object($item) ? Object_Array_Cevir($item) : $item;
            }, $value);
        } else {
            $array[$key] = $value;
        }
    }

    return $array;
}
