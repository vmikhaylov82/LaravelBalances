
Ендпоинты для тестирования в postman:

1. зачисление средств
post
http://localhost/LaravelBalances/public/api/cashIn
{  
    "user_id": 1,  
    "sum": 1200
} 

2. списание средств
post
http://localhost/LaravelBalances/public/api/cashOut
{  
    "user_id": 1,  
    "sum": 1200
} 

3. перевод
post
http://localhost/LaravelBalances/public/api/transfer
{  
    "user_id": 1,  
    "sum": 12,
    "user_id_transfer" : 2
} 

4. получение баланса
get
http://localhost/LaravelBalances/public/api/getBalance?user_id=1

5. конвертация валюты
get
http://localhost/LaravelBalances/public/api/currencyConverter?user_id=1

6. список транзакций
get
http://localhost/LaravelBalances/public/api/listTransactions
{  
    "user_id": 1,  
    "orderSum": true,
    "filterDate" : true,
	"created_at": "2024-12-01"
} 
  
  
Для запуска проекта необходимо:

1.  в консоле перейти в директорию, куда будет скачиваться репозиторий
2.  скачать удаленный репозиторий  
    git clone  [https://github.com/vmikhaylov82/LaravelBalances.git](https://github.com/vmikhaylov82/LaravelBalances.git)
3.  перейти в папку LaravelBalances и запустить docker-compose  
    docker-compose up -d
4.  загрузить зависимости проекта:  
    docker exec -it php bash  
    cd html  
    composer install
5.  создать миграции:  
    php artisan migrate  
6.  запустить проект:  
    перейти в браузере на localhost
