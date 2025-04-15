# Laravel Proje Mimari Diyagramları

Bu klasör, Laravel projesinin mimarisini anlamak için oluşturulmuş Mermaid formatında diyagramları içerir. Bu diyagramlar projenin farklı yönlerini ve bileşenlerini gösterir.

## İçindekiler

1. [Genel Mimari Diyagramı](architecture-diagram.md)
2. [Sınıf Diyagramı](class-diagram.md)
3. [Sıralı Diyagram](sequence-diagram.md)
4. [Bağımlılık Enjeksiyonu Diyagramı](dependency-injection-diagram.md)
5. [Make Full Model Diyagramı](make-full-model-diagram.md)

## Diyagram Açıklamaları

### 1. Genel Mimari Diyagramı (architecture-diagram.md)

Bu diyagram, projenin katmanlı mimarisini, teknoloji stack'ını, modüllerini ve klasör yapısını gösterir. Aşağıdaki bölümleri içerir:

- **Katmanlı Mimari**: Controller -> Service -> Repository -> Model -> Database akışını gösterir
- **Teknoloji Stack'i**: Laravel 11.x, PostgreSQL, Redis, JWT Auth gibi teknolojileri listeler
- **Modüller**: Admin, API ve Auth modüllerini gösterir
- **İstek Yaşam Döngüsü**: HTTP isteğinden API yanıtına kadar akışı gösterir
- **Dependency Injection**: Interface ve bağımlılık enjeksiyonu mekanizmasını gösterir
- **Veri Modeli**: Ana veri modellerini ve ilişkilerini gösterir
- **Repository Pattern**: Repository pattern uygulamasını gösterir
- **Service Pattern**: Service pattern uygulamasını gösterir
- **API Response Pattern**: API yanıt formatını gösterir
- **Çok Dilli Yapı**: Çoklu dil desteğini gösterir
- **Dosya Organizasyonu**: Proje klasörlerinin organizasyonunu gösterir
- **Automatic Code Generation**: Kod üretim akışını gösterir
- **Route Management**: Rota yönetimini gösterir

### 2. Sınıf Diyagramı (class-diagram.md)

Bu diyagram, projedeki temel sınıfları, arayüzleri ve bunlar arasındaki ilişkileri gösterir:

- Base sınıflar (BaseController, BaseRepository, BaseService, BaseRequest)
- Interface yapıları (BaseRepositoryInterface, BaseServiceInterface)
- Trait yapıları (ApiResponseTrait)
- Model örnekleri
- Modül örnekleri (Admin, API, Auth Controllers)
- Service Provider yapısı
- Command yapısı (MakeFullModelCommand)
- Sınıflar arasındaki kalıtım ve uygulama ilişkileri

### 3. Sıralı Diyagram (sequence-diagram.md)

Bu diyagram, bir HTTP isteğinin sistemde işlenme akışını gösterir:

- İstemciden gelen HTTP isteği
- Router tarafından rota eşleştirmesi
- Middleware kontrolleri
- Request validasyonu
- Controller tarafından isteğin işlenmesi
- Service mantığı
- Repository sorguları
- Model ile veritabanı işlemleri
- Resource dönüşümü ve yanıt

### 4. Bağımlılık Enjeksiyonu Diyagramı (dependency-injection-diagram.md)

Bu diyagram, Laravel'in bağımlılık enjeksiyonu ve Service Provider mekanizmasını detaylı bir şekilde gösterir:

- Service Provider yapısı ve registerBindings metodu
- Interface'lerin container'a bağlanması
- Controller -> Service -> Repository -> Model bağımlılık zinciri
- Bir örnek istek akışı boyunca bağımlılık çözümlemesi

### 5. Make Full Model Diyagramı (make-full-model-diagram.md)

Bu diyagram, özel `make:full-model` komutunun çalışma şeklini gösterir:

- Komut çağrısı ve parametreler
- Admin ve API modülleri için oluşturulan dosyalar
- Dosya oluşturma sırası
- Şablon metotları ve bağlantıları

## Diyagramların Gösterilmesi

Bu diyagramları görüntülemek için Mermaid destekleyen bir araç veya web sitesi kullanabilirsiniz:

1. [Mermaid Live Editor](https://mermaid.live/)
2. VS Code için Mermaid eklentisi
3. GitHub (Mermaid diyagramlarını doğrudan destekler)

## Notlar

- Diyagramlar, projenin mevcut mimarisini temel alır
- Diyagramlar, katmanlı mimari, repository pattern ve service pattern gibi proje prensiplerine uygun olarak hazırlanmıştır
- Mimari değişiklikleri yansıtmak için diyagramları güncel tutmak önemlidir 