/**
 * İstek Yönetim Sistemleri
 * - istekCache: Response cache (5dk otomatik temizleme)
 * - istekZamanlayici: Debounce/throttle sistemi
 * - istekGecmisi: Rate limiting kontrolü
 * - dinleyiciler: İstek öncesi/sonrası dinleyiciler
 */
const istekCache = new Map();
const istekZamanlayici = new Map();
const istekGecmisi = new Map();
const dinleyiciler = { oncesi: [], sonrasi: [] };
const cacheTemizlikSuresi = 5 * 60 * 1000;

// Cache otomatik temizleme
setInterval(() => {
    const simdi = Date.now();
    for (const [anahtar, deger] of istekCache.entries()) {
        if (simdi - deger.zaman > cacheTemizlikSuresi)
            istekCache.delete(anahtar);
    }
}, 60000);

/**
 * API İstek Fonksiyonu
 *
 * Gelişmiş API istek fonksiyonu. SweetAlert2 bildirimleri, retry mekanizması,
 * cache, rate limiting, debounce, dinleyiciler ve detaylı hata yönetimi içerir.
 *
 * @param {Object} veri - Gönderilecek veri
 * @param {Object} secenekler - İstek ayarları
 * @param {string} secenekler.metod - HTTP metodu (GET/POST, varsayılan: POST)
 * @param {boolean} secenekler.bildirim - SweetAlert2 bildirimi göster (varsayılan: false)
 * @param {number} secenekler.zamanasimiSuresi - Timeout süresi ms (varsayılan: 30000)
 * @param {number} secenekler.yeniden - Hata durumunda kaç kez tekrar dene (varsayılan: 0)
 * @param {number} secenekler.yenidenBekle - Retry arası bekleme ms (varsayılan: 1000)
 * @param {boolean} secenekler.cache - Response cache kullan (varsayılan: false)
 * @param {number} secenekler.cacheSure - Cache süresi ms (varsayılan: 300000 - 5dk)
 * @param {number} secenekler.rateLimit - Aynı istek arası min ms (varsayılan: 0)
 * @param {number} secenekler.debounce - Debounce süresi ms (varsayılan: 0)
 * @returns {Promise<Object>} API yanıtı
 *
 * @example
 * await istek({islem: 'kaydet', id: 123}, {bildirim: true, yeniden: 3})
 * await istek({islem: 'ara', q: 'test'}, {cache: true, debounce: 500})
 * await istek({islem: 'sil'}, {rateLimit: 2000})
 */
const istek = async (veri, secenekler = {}) => {
    // Güvenlik kontrolleri
    if (!TOKEN || !API_URL) {
        throw new Error("Güvenlik: TOKEN veya API_URL tanımlı değil!");
    }

    if (!veri || typeof veri !== "object" || !ZAMAN) {
        throw new Error("Geçersiz veri formatı!");
    }

    // İstek ayarlarını al
    const metod = secenekler.metod || "POST";
    const bildirimGoster = secenekler.bildirim || false;
    const zamanasimiSuresi = secenekler.zamanasimiSuresi || 30000;
    const yenidenDeneme = secenekler.yeniden || 0;
    const yenidenBekleme = secenekler.yenidenBekle || 1000;
    const cacheKullan = secenekler.cache || false;
    const cacheSure = secenekler.cacheSure || 300000;
    const rateLimit = secenekler.rateLimit || 0;
    const debounce = secenekler.debounce || 0;

    // Cache anahtarı oluştur (hash ile)
    const cacheStr = JSON.stringify({ veri, metod });
    let hash = 0;
    for (let i = 0; i < cacheStr.length; i++) {
        hash = (hash << 5) - hash + cacheStr.charCodeAt(i);
        hash = hash & hash;
    }
    const cacheAnahtari = Math.abs(hash).toString(36);
    if (GELISTIRICI) console.log("🔑 Cache Key:", cacheAnahtari);

    // Debounce kontrolü
    if (debounce > 0) {
        if (istekZamanlayici.has(cacheAnahtari)) {
            clearTimeout(istekZamanlayici.get(cacheAnahtari));
        }
        return new Promise((resolve) => {
            const timer = setTimeout(async () => {
                istekZamanlayici.delete(cacheAnahtari);
                const sonuc = await istek(veri, { ...secenekler, debounce: 0 });
                resolve(sonuc);
            }, debounce);
            istekZamanlayici.set(cacheAnahtari, timer);
        });
    }

    // Rate Limiting kontrolü
    if (rateLimit > 0) {
        const sonIstek = istekGecmisi.get(cacheAnahtari);
        if (sonIstek) {
            const gecenSure = Date.now() - sonIstek;
            if (gecenSure < rateLimit) {
                return {
                    hata: true,
                    mesaj: `Çok hızlı istek. ${Math.ceil(
                        (rateLimit - gecenSure) / 1000
                    )}s bekleyin.`,
                    kod: 429,
                };
            }
        }
        istekGecmisi.set(cacheAnahtari, Date.now());
    }

    // Cache kontrolü
    if (cacheKullan && metod === "GET") {
        const cachedData = istekCache.get(cacheAnahtari);
        if (cachedData && Date.now() - cachedData.zaman < cacheSure) {
            if (GELISTIRICI)
                console.log("📦 Cache'den getiriliyor:", cacheAnahtari);
            return cachedData.veri;
        }
    }

    // Dinleyiciler: Öncesi
    for (const dinleyici of dinleyiciler.oncesi) {
        try {
            await dinleyici({ veri, secenekler });
        } catch (error) {
            console.warn("Dinleyici (öncesi) hatası:", error);
        }
    }

    try {
        // Timeout kontrolü için AbortController
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
        }, zamanasimiSuresi);

        // İstek işlemleri burada yapılacak
        // Örnek: fetch API kullanarak istek gönderme
        const parametreler = new URLSearchParams(veri);
        parametreler.append("token", TOKEN);
        parametreler.append("zaman", ZAMAN);

        // Adresi belirle
        const adres = metod === "GET" ? `${API_URL}?${parametreler}` : API_URL;

        // Gövdeyi belirle
        const govde = metod === "GET" ? null : parametreler.toString();

        const yanit = await fetch(adres, {
            method: metod,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                Authorization: `Bearer ${TOKEN}`,
                "Cache-Control": "no-cache, no-store, must-revalidate",
                Pragma: "no-cache",
                Expires: "0",
            },
            body: govde,
            signal: controller.signal,
            cache: "no-store",
            credentials: "same-origin",
        });

        // Timeout'u temizle
        clearTimeout(timeoutId);

        // Gelen yanıtı işle
        const metin = await yanit.text();

        // Yanıtı JSON olarak ayrıştır
        const sonuc = jsonKontrol(metin) ? JSON.parse(metin) : metin;

        // API'den gelen standart mesaj formatını otomatik bildirim göster
        if (bildirimGoster && sonuc?.kod) {
            bildirim(sonuc);
        }

        // HTTP Hata kontrolü
        if (!yanit.ok) {
            console.dir(sonuc);
            const status = sonuc.kod || yanit.status;
            const statusText = sonuc.mesaj || yanit.statusText;
            //const hata = new Error(`${status}: ${statusText}`);
            const hata = new Error(`${status}: ${statusText}`);
            hata.status = status;
            hata.statusText = statusText;
            hata.yanit = yanit;
            throw hata;
        }

        if (!metin) return null;

        // Cache'e kaydet
        if (cacheKullan && metod === "GET") {
            istekCache.set(cacheAnahtari, {
                veri: sonuc,
                zaman: Date.now(),
            });
            if (GELISTIRICI)
                console.log("💾 Cache'e kaydedildi:", cacheAnahtari);
        }

        // Dinleyiciler: Sonrası
        for (const dinleyici of dinleyiciler.sonrasi) {
            try {
                await dinleyici({ veri, secenekler, sonuc });
            } catch (error) {
                console.warn("Dinleyici (sonrası) hatası:", error);
            }
        }

        if (GELISTIRICI) console.log(`API Yanıtı:`, sonuc);
        return sonuc;
    } catch (hata) {
        console.group("🛑 API İstek Hatası");
        console.error("Tip:", hata.name, "| Mesaj:", hata.message);
        if (hata.status) console.error("HTTP Status:", hata.status);
        console.groupEnd();

        // Timeout hatası
        if (hata.name === "AbortError") {
            return {
                hata: true,
                mesaj: `İstek zaman aşımına uğradı (${zamanasimiSuresi}ms)`,
                kod: 408,
            };
        }

        // Network bağlantı hatası
        if (hata.name === "TypeError" && hata.message.includes("fetch")) {
            // Retry mekanizması
            if (yenidenDeneme > 0) {
                if (GELISTIRICI)
                    console.log(
                        `🔄 Yeniden deneniyor... (${yenidenDeneme} kez daha)`
                    );
                await new Promise((resolve) =>
                    setTimeout(resolve, yenidenBekleme)
                );
                return istek(veri, {
                    ...secenekler,
                    yeniden: yenidenDeneme - 1,
                });
            }

            return {
                hata: true,
                mesaj: "Sunucuya bağlanılamıyor. İnternet bağlantınızı kontrol edin.",
                kod: 0,
            };
        }

        // HTTP hatası kontrolü
        if (hata.status) {
            // 5xx hatalarında retry
            if (hata.status >= 500 && hata.status < 600 && yenidenDeneme > 0) {
                if (GELISTIRICI)
                    console.log(
                        `🔄 Sunucu hatası, yeniden deneniyor... (${yenidenDeneme} kez daha)`
                    );
                await new Promise((resolve) =>
                    setTimeout(resolve, yenidenBekleme)
                );
                return istek(veri, {
                    ...secenekler,
                    yeniden: yenidenDeneme - 1,
                });
            }

            return {
                hata: true,
                mesaj: hata.message,
                kod: hata.status,
            };
        }

        // Diğer hatalar
        return {
            hata: true,
            mesaj: hata.message || "Bilinmeyen istek hatası",
            kod: 500,
        };
    }
};

// Dinleyici ekleme fonksiyonları (istek fonksiyonu tanımlandıktan sonra)
istek.dinle = {
    oncesi: (fn) => dinleyiciler.oncesi.push(fn),
    sonrasi: (fn) => dinleyiciler.sonrasi.push(fn),
    temizle: () => {
        dinleyiciler.oncesi = [];
        dinleyiciler.sonrasi = [];
    },
};

/**
 * JSON Veri Doğrulama Fonksiyonu
 *
 * Bu fonksiyon, verilen değerin geçerli JSON formatında olup olmadığını
 * kontrol eder ve Boolean sonuç döndürür.
 *
 * Kullanım Alanı:
 * - API yanıtlarının validasyonunda
 * - JSON parse öncesi kontrollerde
 * - Veri tiplerinin doğrulanmasında
 * - Error handling işlemlerinde
 *
 * Teknik Detaylar:
 * - Null ve undefined değerler için özel handling
 * - String, number, boolean, object tipleri destekler
 * - JSON.parse/stringify exception handling
 * - NaN ve Infinity kontrolleri
 *
 * Kullanım Örneği:
 * if (jsonKontrol(response)) {
 *     const data = JSON.parse(response);
 * }
 * const isValid = jsonKontrol({name: 'test'}); // true
 *
 * @param {any} veri Kontrol edilecek veri
 * @returns {boolean} Geçerli JSON ise true, değilse false
 */
const jsonKontrol = (veri) => {
    if (veri === null || veri === undefined) {
        return veri === null;
    }

    if (veri === "") {
        return false;
    }

    if (typeof veri === "number" || typeof veri === "boolean") {
        if (typeof veri === "number" && (!isFinite(veri) || isNaN(veri))) {
            return false;
        }
        return true;
    }

    if (typeof veri === "string") {
        try {
            JSON.parse(veri);
            return true;
        } catch (error) {
            return false;
        }
    }

    if (typeof veri === "object") {
        try {
            JSON.stringify(veri);
            return true;
        } catch (error) {
            return false;
        }
    }

    return false;
};

/**
 * API Yanıt Bildirim Sistemi
 *
 * API'den gelen standart mesaj formatını
 * SweetAlert2 ile görselleştirir. API'den dönen mesaj yapısı
 * ile tam entegre çalışır.
 *
 * Kullanım Şekilleri:
 * 1. API yanıtı ile: bildirim(apiYanit)
 * 2. Kolay metodlar: bildirim.basarili('İşlem tamam')
 * 3. Kolay metodlar (detaylı): bildirim.hata('Hata oluştu', 'anasayfa')
 * 4. Eski format: bildirim('Başlık', 'Mesaj', '', 200)
 *
 * Kod Karşılıkları:
 * - 200: Başarılı işlem (success) - bildirim.basarili()
 * - 202: Bilgilendirme (info) - bildirim.bilgi()
 * - 204: Sessiz başarı - bildirim.sessiz()
 * - 401: Oturum hatası (warning) - bildirim.oturum()
 * - 403: Yetki hatası (error) - bildirim.yetki()
 * - 404: Hata mesajı (error) - bildirim.hata()
 * - 409: Veri çakışması (warning) - bildirim.cakisma()
 * - 418: Debug bilgisi (question) - bildirim.debug()
 * - 422: Validasyon hatası (error) - bildirim.validasyon()
 * - 429: Çok fazla istek (warning) - bildirim.limit()
 * - 500: Dikkat mesajı (warning) - bildirim.dikkat()
 * - 503: Servis hatası (error) - bildirim.servis()
 *
 * Kullanım Örnekleri:
 * bildirim.basarili('Kayıt başarılı', 'dashboard');
 * bildirim.hata('Bir sorun oluştu');
 * bildirim.yetki('Bu işlem için yetkiniz yok');
 * bildirim.bilgi('İşleminiz alındı', 'yenile');
 * bildirim.debug('Test değeri: ' + x);
 *
 * @param {Object|string} veri - API yanıtı (object) veya başlık (string - eski format)
 * @param {string} mesaj - Mesaj metni (sadece eski format için)
 * @param {string} url - Yönlendirme URL'i (sadece eski format için)
 * @param {number} kod - HTTP status kodu (sadece eski format için)
 * @returns {boolean} İşlem başarılı ise true, değilse false
 */
const bildirim = function (veri, mesaj, url, kod) {
    // SweetAlert2 kontrolü
    if (typeof Swal === "undefined") {
        console.warn("SweetAlert2 kütüphanesi yüklü değil!");
        console.info("Mesaj:", veri);
        return false;
    }

    let _baslik, _mesaj, _url, _kod, _icon;

    // Yeni format: Object parametresi (API'den gelen standart mesaj formatı)
    if (typeof veri === "object" && veri !== null) {
        _kod = veri.kod;
        _baslik = veri.baslik || "Bildirim";
        _mesaj = veri.mesaj || "";
        _url = veri.url || "";
    }
    // Eski format: Ayrı parametreler (geriye dönük uyumluluk)
    else if (typeof veri === "string") {
        _baslik = veri;
        _mesaj = mesaj || "";
        _url = url || "";
        _kod = kod;
    }
    // Geçersiz format
    else {
        console.error("Geçersiz bildirim formatı:", veri);
        return false;
    }

    // Kod'a göre icon belirleme
    switch (_kod) {
        case 200: // Başarılı (basarili)
            _icon = "success";
            break;
        case 202: // Bilgi (bilgi)
            _icon = "info";
            break;
        case 204: // Sessiz başarı (sessiz) - bildirim gösterilmez
            return true; // Erken çıkış, bildirim gösterme
        case 401: // Oturum Hatası (oturum)
            _icon = "warning";
            break;
        case 403: // Yetki Hatası (yetki)
            _icon = "error";
            break;
        case 404: // Hata (hata)
            _icon = "error";
            break;
        case 409: // Veri Çakışması (cakisma)
            _icon = "warning";
            break;
        case 418: // Debug Bilgisi (debug)
            _icon = "question";
            break;
        case 422: // Validasyon Hatası (validasyon)
            _icon = "error";
            break;
        case 429: // Çok Fazla İstek (limit)
            _icon = "warning";
            break;
        case 500: // Dikkat (dikkat)
            _icon = "warning";
            break;
        case 503: // Servis Hatası (servis)
            _icon = "error";
            break;
        default:
            _icon = "info";
            break;
    }

    // SweetAlert2 bildirimi göster
    Swal.fire({
        icon: _icon,
        title: _baslik,
        html: _mesaj,
        confirmButtonText: "Tamam",
        showCloseButton: true,
        heightAuto: false,
        allowOutsideClick: true,
        allowEscapeKey: true,
    }).then((result) => {
        // Kullanıcı butona tıkladıysa veya kapatdıysa
        if (result.isConfirmed || result.isDismissed) {
            // Yönlendirme kontrolü
            if (_url === "yenile") {
                location.reload();
            } else if (_url && _url !== "") {
                window.location.href = _url;
            }
        }
    });

    return true;
};

// Kolay kullanım metodları
bildirim.basarili = function (mesaj, url = "") {
    return bildirim({ kod: 200, baslik: "Başarılı", mesaj, url });
};

bildirim.bilgi = function (mesaj, url = "") {
    return bildirim({ kod: 202, baslik: "Bilgi", mesaj, url });
};

bildirim.sessiz = function (mesaj = "") {
    return bildirim({ kod: 204, baslik: "", mesaj, url: "" });
};

bildirim.oturum = function (mesaj, url = "") {
    return bildirim({ kod: 401, baslik: "Oturum Hatası", mesaj, url });
};

bildirim.yetki = function (mesaj, url = "") {
    return bildirim({ kod: 403, baslik: "Yetki Hatası", mesaj, url });
};

bildirim.hata = function (mesaj, url = "") {
    return bildirim({ kod: 404, baslik: "Hata", mesaj, url });
};

bildirim.cakisma = function (mesaj, url = "") {
    return bildirim({ kod: 409, baslik: "Veri Çakışması", mesaj, url });
};

bildirim.debug = function (mesaj, url = "") {
    return bildirim({ kod: 418, baslik: "Debug Bilgisi", mesaj, url });
};

bildirim.validasyon = function (mesaj, url = "") {
    return bildirim({ kod: 422, baslik: "Geçersiz Veri", mesaj, url });
};

bildirim.limit = function (mesaj, url = "") {
    return bildirim({ kod: 429, baslik: "Çok Fazla İstek", mesaj, url });
};

bildirim.dikkat = function (mesaj, url = "") {
    return bildirim({ kod: 500, baslik: "Dikkat", mesaj, url });
};

bildirim.servis = function (mesaj, url = "") {
    return bildirim({ kod: 503, baslik: "Servis Kullanılamıyor", mesaj, url });
};
