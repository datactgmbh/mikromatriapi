# MikroMatriApi

This is a simple API for the Mikrotik Product matrix.
It downloads the product matrix from the Mikrotik website and saves it to a local database which is then used to query the product matrix via API.

### Requirements

- PHP > 8.4
- SQLite, MySQL or PostgreSQL

### Installation

1. Download the project
2. Unzip the folder in your web server root directory
3. Create a new database in your MySQL or PostgreSQL database server
4. Point your browser to your web server root directory
5. Follow the instructions on the screen
6. Configure the cron job to download the product matrix every day

### CLI

You can use the CLI to download the product matrix.

```
php bin/console app:download-product-matrix
```

### Cron job

The cron job is used to download the product matrix every day.

```
0 0 * * * cd /path/to/mikrotik_product_matrix && php bin/console app:download-product-matrix
```

### API

The API is very simple.

```
GET /api/products
```

List all products.
```
GET /api/products
```

Get a product by code.
```
GET /api/products/{code}
```

Get only the product code and architecture.

```
GET /api/products/{code}/architecture
```


### License

This project is licensed under the AGPL-3.0 license.

### Manually download the product matrix

You can manually download the product matrix by running the following command:

```bash
curl -L \
  -e "https://mikrotik.com/products/matrix" \
  -d "ax=matrix&ax_group=" \
  -o product_matrix.csv \
  "https://mikrotik.com/products"
```

### Copyright

Copyright (C) 2025 [DatACT GmbH](https://www.datact.ch/)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published
by the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

