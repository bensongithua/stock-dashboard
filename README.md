# Stock Dashboard Interview Project

A Laravel 12 + MySQL project for uploading CSV stock data and visualizing top 5 stock performers.

## Setup Instructions

1. Clone the repo

    git clone https://github.com/bensongithua/stock-dashboard.git

    cd stock-dashboard

2. Install dependencies

    composer install

    npm install

    npm run dev

3. Create `.env` file and configure MySQL credentials
4. Run migrations

    php artisan migrate

5. Start the server

    php artisan serve

## Features

-   Upload CSV of stock prices
-   Calculate and display top 5 gainers
-   Interactive chart with Chart.js

Developed by **Benson Githinga**
