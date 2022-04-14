cd sql

PGPASSWORD=u3ue7itosyz79gqt psql -U doadmin -h db-postgresql-sfo3-67017-do-user-441183-0.a.db.ondigitalocean.com -p 25060 -d defaultdb --set=sslmode=require -f process.sql