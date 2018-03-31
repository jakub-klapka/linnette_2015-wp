Tester is expecting dev running on `https://linnette.test`

### Install testcafe:
`yarn install`

### Backup current DB:
ssh: `mysqldump -u root -p linnette > dev_db_dump.sql`

### Before every run:
ssh: `mysql -u root -p linnette < wp-content/themes/linnette/tests/test_db.sql`

### Run tests:
`yarn test`

### Restore dev DB:
ssh: `mysql -u root -p linnette < dev_db_dump.sql`