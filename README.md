# App Boilerplate [Metronic 2018]

This is the boilerplate for any EIU web app being developed. It is based on the Metronic theme. Also uses .env variables for storing password and other sensitive data. Updated mysqli class as well.

## Project Timeline [for apps in development]
| Step         | Date    |
|--------------|---------|
| App Backend  | 4/15/20 |
| Frontend App | 4/15/20 |
| Contract     | 4/25/20 |
| Nomination   | 5/1/20  |

## For New Apps
- Read the [New App Checklist](https://github.com/easternillinois/WebDocs/wiki/New-App-Checklist)

## Project Scope
[View the Project Scope](https://docs.google.com/document/d/17uk6JY1FiLgSw4u1UzaF9UywTK51LtSv9vFGgyx6rOI/edit#)

## Team
- Nate Atkinson
- Ryan Gibson
- Lucas Lower
- Francesco Romano

## Client Contact
- [Ryan Gibson](mailto:rwgibson@eiu.edu) - Web Office

## Web
Backend: 	https://www.eiu.edu/apps/metronic/

Frontend: 	https://www.eiu.edu/

## Components
- MySQL/MySQLI
- ORACLE
- PHP
- FPDF
- PHPEXCEL

## DATA SOURCES

### cats_metronic ([class](https://github.com/easternillinois/_AppBoilerplateMetronic/blob/main/include/mysqli_class.php))
	- MySQL
	- user: cats
	- stores all data
	
### eiumisc.EIU_APPLICANTS ([class](https://github.com/easternillinois/_AppBoilerplateMetronic/blob/main/include/oracle_class.php))
	- ORACLE
	- user: eiu_webctuser
	- pulls checklist data and other admissions/housing/financial aid data

## ERD
![Disability](https://github.com/easternillinois/disability-services/blob/main/_info/disability.png "Disability ERD")