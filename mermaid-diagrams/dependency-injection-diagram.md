```mermaid
graph TD
    %% Service Provider Yapısı
    subgraph "Service Provider Mekanizması"
        APP[Laravel Uygulaması] --> PROVIDERS[Service Providers]
        PROVIDERS --> REPO_PROVIDER[RepositoryServiceProvider]
        PROVIDERS --> AUTH_PROVIDER[AuthServiceProvider]
        PROVIDERS --> OTHER_PROVIDERS[Diğer Service Providers]
        
        REPO_PROVIDER --> BIND_METHOD[bindAllInterfaces()]
        BIND_METHOD --> NAMESPACE_LOOP["Namespace Loop (Admin, Api, Auth)"]
        
        NAMESPACE_LOOP --> GET_INTERFACES[getInterfaces() Metodu]
        GET_INTERFACES --> INTERFACES_LIST[Interface Listesi]
        
        INTERFACES_LIST --> BIND_LOOP["Interface için Bağlama Döngüsü"]
        BIND_LOOP --> EXTRACT_NAME["Model Adını Çıkar (str_replace)"]
        EXTRACT_NAME --> CREATE_CLASS["Concrete Class Adını Oluştur"]
        CREATE_CLASS --> CHECK_EXISTS["Class var mı? (class_exists)"]
        
        CHECK_EXISTS -- Evet --> CONTAINER_BIND["Container'a Bağla (app->bind)"]
        CONTAINER_BIND --> NEXT_INTERFACE["Sonraki Interface"]
        CHECK_EXISTS -- Hayır --> NEXT_INTERFACE
        
        NEXT_INTERFACE --> BIND_LOOP
    end
    
    %% Bağlantılar ve Çözümleme
    subgraph "Bağımlılık Çözümleme"
        CONTROLLER_CONST[Controller Constructor] --> TYPE_HINT["Tip İpucu (ServiceInterface)"]
        TYPE_HINT --> CONTAINER_RESOLVE["Container Çözümleme"]
        CONTAINER_RESOLVE --> FIND_BINDING["Bağlantıyı Bul"]
        FIND_BINDING --> CREATE_INSTANCE["Service Nesnesini Oluştur"]
        
        CREATE_INSTANCE --> SERVICE_CONST["Service Constructor"]
        SERVICE_CONST --> REPO_TYPE_HINT["Tip İpucu (RepositoryInterface)"]
        REPO_TYPE_HINT --> REPO_CONTAINER_RESOLVE["Repository için Container Çözümleme"]
        REPO_CONTAINER_RESOLVE --> REPO_FIND_BINDING["Repository Bağlantısını Bul"]
        REPO_FIND_BINDING --> REPO_CREATE_INSTANCE["Repository Nesnesini Oluştur"]
        
        REPO_CREATE_INSTANCE --> MODEL_DI["Model Nesnesi DI"]
    end
    
    %% Servis Container Örneği
    subgraph "Somut Örnek"
        EXAMPLE_CONTAINER["Service Container"] --> EXAMPLE_BINDING["Interface:Concrete Bağlantıları"]
        
        EXAMPLE_BINDING --> USER_SERVICE_BINDING["UserServiceInterface:UserService"]
        EXAMPLE_BINDING --> USER_REPO_BINDING["UserRepositoryInterface:UserRepository"]
        
        REQUEST[HTTP İsteği] --> USER_CONTROLLER["UserController Constructor"]
        USER_CONTROLLER --> DI_USER_SERVICE["DI: UserServiceInterface $service"]
        DI_USER_SERVICE --> RESOLVE_USER_SERVICE["Container UserService'i çözümler"]
        
        RESOLVE_USER_SERVICE --> USER_SERVICE_CONSTRUCT["UserService Constructor"]
        USER_SERVICE_CONSTRUCT --> DI_USER_REPO["DI: UserRepositoryInterface $repository"]
        DI_USER_REPO --> RESOLVE_USER_REPO["Container UserRepository'i çözümler"]
        
        RESOLVE_USER_REPO --> USER_REPO_CONSTRUCT["UserRepository Constructor"]
        USER_REPO_CONSTRUCT --> DI_USER_MODEL["DI: User $model"]
    end
    
    %% Bir örnek istekte sınıfların kullanımı
    subgraph "İstek İşleme Akışı"
        API_REQUEST[API İsteği] --> ROUTE_USER["/api/users/{id}"]
        ROUTE_USER --> USER_CONTROLLER_SHOW["UserController@show($id)"]
        
        USER_CONTROLLER_SHOW --> SERVICE_FIND["$this->service->find($id)"]
        SERVICE_FIND --> REPO_FIND["$this->repository->find($id)"]
        REPO_FIND --> MODEL_FIND["$this->model->find($id)"]
        
        MODEL_FIND --> DB_QUERY["DB Query: SELECT * FROM users WHERE id = ?"]
        DB_QUERY --> USER_OBJECT["User Model Nesnesi"]
        
        USER_OBJECT --> SERVICE_RETURN["Service'den Controller'a Dönüş"]
        SERVICE_RETURN --> RESOURCE_TRANSFORM["UserResource üzerinden dönüşüm"]
        RESOURCE_TRANSFORM --> JSON_RESPONSE["JSON Response"]
    end
    
    %% Akış
    BIND_METHOD -.-> EXAMPLE_CONTAINER
    CONTAINER_BIND -.-> EXAMPLE_BINDING
    EXAMPLE_CONTAINER -.-> CONTAINER_RESOLVE
``` 