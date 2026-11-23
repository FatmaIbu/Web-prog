# FurniEra – Furniture E-Commerce Website

FurniEra is a furniture e-commerce web application developed for the Web Programming course at International Burch University.

The project uses a Single Page Application (SPA) architecture and is being developed through multiple milestones.

## Project Overview

FurniEra provides a responsive furniture shopping interface with product collections, category filtering, shopping cart features, and smooth navigation.

## Milestone 1 – Frontend Development

- Responsive design using HTML5, CSS3, and Bootstrap.
- SPA navigation using jQuery and SPApp.
- Home, Shop, About, Blog, Contact, Cart, and Checkout pages.
- Product filtering and testimonial slider.
- Shopping cart interface and checkout layout.
- Frontend form validation.

**Status:** Implemented.

## Milestone 2 – Database and DAO Layer

- MySQL database: `furniera_db`.
- Five entities: Users, Products, Categories, Orders, and Reviews.
- Primary and foreign key relationships.
- SQL database export with sample records.
- PHP database connection using PDO.
- BaseDAO class and five entity-specific DAO classes.
- CRUD operations using prepared statements.
- Database connection and DAO testing scripts.

### Testing

- Database connection test passed.
- ProductDAO READ test passed.
- ProductDAO full CRUD test passed (Create, Read, Update, Delete).

**Status:** Database and DAO layer implemented. Additional DAO tests are planned.

## Technologies Used

**Frontend:** HTML5, CSS3, JavaScript, Bootstrap, jQuery, SPApp, Tiny Slider.

**Backend:** PHP, MySQL, PDO.

**Planned:** FlightPHP, RESTful APIs, OpenAPI/Swagger documentation, authentication, and authorization.

## Project Structure

```text
FurniEra-1/
├── furni-1.0.0/
│   ├── Frontend/
│   │   ├── css/
│   │   ├── images/
│   │   ├── js/
│   │   └── views/
│   │       ├── index.html
│   │       └── pages/
│   │           ├── home.html
│   │           ├── shop.html
│   │           ├── about.html
│   │           ├── blog.html
│   │           ├── contact.html
│   │           ├── cart.html
│   │           └── checkout.html
│   └── Backend/
│       ├── config/
│       ├── dao/
│       ├── database/
│       ├── tests/
│       ├── services/
│       ├── routes/
│       ├── middleware/
│       └── logs/
└── README.md
```

## Development Roadmap

### Milestone 1
Frontend SPA implementation — completed.

### Milestone 2
MySQL database and PHP DAO layer — implemented.

### Milestone 3
Service layer, FlightPHP REST API endpoints, OpenAPI documentation, and further integration.

## Repository

https://github.com/FatmaIbu/Web-prog

## Author

Fatma Younes

International Burch University – Web Programming