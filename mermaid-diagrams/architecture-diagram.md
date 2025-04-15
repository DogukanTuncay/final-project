
graph TB
    %% Ana Katmanlar
    subgraph "Katmanlı Mimari"
        CLIENT[İstemci] --> ROUTER[Router]
        ROUTER --> CONTROLLER[Controller]
        CONTROLLER --> SERVICE[Service]
        SERVICE --> REPOSITORY[Repository]
        REPOSITORY --> MODEL[Model]
        MODEL --> DATABASE[(Database)]
    end

    %% Proje Bileşenleri
    subgraph "Teknoloji Stack"
        LARAVEL[Laravel 11.x]
        POSTGRESQL[PostgreSQL]
        REDIS[Redis]
        JWT[JWT Auth]
        SPATIE[Spatie Laravel Translatable]
    end

    %% Modüller
    subgraph "Modüller"
        ADMIN_MODULE[Admin Modülü]
        API_MODULE[API Modülü]
        AUTH_MODULE[Auth Modülü]
    end

    %% İstek Yaşam Döngüsü
    subgraph "İstek Yaşam Döngüsü"
        REQUEST[HTTP İsteği] --> MIDDLEWARE[Middleware]
        MIDDLEWARE --> REQUEST_VALIDATION[Request Validation]
        REQUEST_VALIDATION --> CONTROLLER_ACTION[Controller Action]
        CONTROLLER_ACTION --> SERVICE_LOGIC[Service Logic]
        SERVICE_LOGIC --> REPOSITORY_QUERY[Repository Query]
        REPOSITORY_QUERY --> DATABASE_READ[Database Read/Write]
        DATABASE_READ --> MODEL_DATA[Model Data]
        MODEL_DATA --> RESOURCE[Resource]
        RESOURCE --> RESPONSE[API Response]
    end

    %% Interface ve Bağımlılık Enjeksiyonu
    subgraph "Dependency Injection"
        INTERFACE[Interfaces] <--> CONCRETE[Concrete Classes]
        PROVIDER[Service Provider] --> BIND[Bağlama İşlemi]
        BIND --> CONTAINER[Service Container]
        CONTAINER --> RESOLVE[Resolve Dependencies]
    end

    %% Veri Modeli
    subgraph "Veri Modeli"
        USER_MODEL[User]
        COURSE_MODEL[Course]
        CHAPTER_MODEL[CourseChapter]
        LESSON_MODEL[CourseChapterLesson]
        CONTENT_MODEL[CourseChapterLessonContent]
        
        USER_MODEL -- has many --> COURSE_MODEL
        COURSE_MODEL -- has many --> CHAPTER_MODEL
        CHAPTER_MODEL -- has many --> LESSON_MODEL
        LESSON_MODEL -- has many --> CONTENT_MODEL
    end

    %% Repository Pattern Detayı
    subgraph "Repository Pattern"
        BASE_REPO_INTERFACE[BaseRepositoryInterface]
        BASE_REPO[BaseRepository]
        CONCRETE_REPO_INTERFACE[Model RepositoryInterface]
        CONCRETE_REPO[Model Repository]
        
        CONCRETE_REPO_INTERFACE  --> BASE_REPO_INTERFACE
        CONCRETE_REPO  --> BASE_REPO
        CONCRETE_REPO_INTERFACE  --> CONCRETE_REPO
    end

    %% Service Pattern Detayı
    subgraph "Service Pattern"
        BASE_SERVICE_INTERFACE[BaseServiceInterface]
        BASE_SERVICE[BaseService]
        CONCRETE_SERVICE_INTERFACE[Model ServiceInterface]
        CONCRETE_SERVICE[Model Service]
        
        CONCRETE_SERVICE_INTERFACE  --> BASE_SERVICE_INTERFACE
        CONCRETE_SERVICE  --> BASE_SERVICE
        CONCRETE_SERVICE_INTERFACE  -->  CONCRETE_SERVICE
    end

    %% API Response Pattern
    subgraph "API Response Pattern"
        SUCCESS[Success Response] --> JSON_SUCCESS["{status: 'success', message: '', errors: {}, data: []}"]
        ERROR[Error Response] --> JSON_ERROR["{status: 'error', message: '', errors: {...}, data: null}"]
    end


    %% Çeviriler ve Dil Yönetimi
    subgraph "Çok Dilli Yapı"
        MODEL_TRANSLATABLE[Translatable Models] --> TR_LANG[Türkçe]
        MODEL_TRANSLATABLE --> EN_LANG[İngilizce]
        LOCALE_MIDDLEWARE[Locale Middleware] --> DETECT_LANG[Dil Tespiti]
        DETECT_LANG --> SET_LOCALE[Set App Locale]
        SET_LOCALE --> TRANSLATE_RESPONSE[Çevrilen Yanıt]
    end

    %% Dosya Yapısı
    subgraph "Dosya Organizasyonu"
        APP_DIR[app/] --> CONTROLLERS_DIR[Controllers/]
        APP_DIR --> SERVICES_DIR[Services/]
        APP_DIR --> REPOSITORIES_DIR[Repositories/]
        APP_DIR --> INTERFACES_DIR[Interfaces/]
        APP_DIR --> MODELS_DIR[Models/]
        APP_DIR --> TRAITS_DIR[Traits/]
        APP_DIR --> REQUESTS_DIR[Requests/]
        APP_DIR --> RESOURCES_DIR[Resources/]
        APP_DIR --> MIDDLEWARE_DIR[Middleware/]
        
        CONTROLLERS_DIR --> ADMIN_CONT[Admin/]
        CONTROLLERS_DIR --> API_CONT[Api/]
        CONTROLLERS_DIR --> AUTH_CONT[Auth/]
        
        SERVICES_DIR --> ADMIN_SERV[Admin/]
        SERVICES_DIR --> API_SERV[Api/]
        SERVICES_DIR --> AUTH_SERV[Auth/]
        
        REPOSITORIES_DIR --> ADMIN_REPO[Admin/]
        REPOSITORIES_DIR --> API_REPO[Api/]
        REPOSITORIES_DIR --> AUTH_REPO[Auth/]
        
        INTERFACES_DIR --> REPO_INT[Repositories/]
        INTERFACES_DIR --> SERV_INT[Services/]
        
        REPO_INT --> ADMIN_REPO_INT[Admin/]
        REPO_INT --> API_REPO_INT[Api/]
        REPO_INT --> AUTH_REPO_INT[Auth/]
        
        SERV_INT --> ADMIN_SERV_INT[Admin/]
        SERV_INT --> API_SERV_INT[Api/]
        SERV_INT --> AUTH_SERV_INT[Auth/]
    end

    %% Kod Oluşturma Akışı
    subgraph "Automatic Code Generation"
        MAKE_FULL_MODEL[make:full-model] --> CREATE_MODEL[Model Creation]
        CREATE_MODEL --> CREATE_MIGRATION[Migration Creation]
        CREATE_MODEL --> CREATE_CONTROLLER[Controller Creation]
        CREATE_MODEL --> CREATE_SERVICE[Service Creation]
        CREATE_MODEL --> CREATE_REPOSITORY[Repository Creation]
        CREATE_MODEL --> CREATE_INTERFACE[Interface Creation]
        CREATE_MODEL --> CREATE_REQUEST[Request Creation]
        CREATE_MODEL --> CREATE_RESOURCE[Resource Creation]
    end

    %% Rota Yönetimi
    subgraph "Route Management"
        WEB_ROUTE[web.php] --> WEB[Web Routes]
        API_ROUTE[api.php] --> API[API Routes]
        ADMIN_ROUTE[admin.php] --> ADMIN[Admin Routes]
        AUTH_ROUTE[auth.php] --> AUTH[Auth Routes]
    end

    %% Açıklamalar
    class CLIENT node-client;
    class DATABASE node-database;
    class INTERFACE interface-type;
    class BASE_SERVICE_INTERFACE interface-type;
    class BASE_REPO_INTERFACE interface-type;
    
    classDef node-client fill:#f9f,stroke:#333,stroke-width:2px;
    classDef node-database fill:#9cf,stroke:#333,stroke-width:2px;
    classDef interface-type fill:#fc9,stroke:#333,stroke-width:2px;
