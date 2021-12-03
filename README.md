# mysqlcompare
a command line tool to compare two mysql databases

![Downloads](https://img.shields.io/packagist/dt/lloadout/mysqlcompare.svg?style=flat-square)

## Requirements

mysqlcompare is a command line tool to compare two database schema's and retreive the difference in an sql file.

## Installation

```shell
composer global require lloadout/mysqlcompare
```

# Usage

## Init

Connection data can be defined in a connections.json file , to create the connection file you run the following command

```shell
mysqlcompare init
```

a `connections.json` file will be created , specifying a source and target connection and an sqlfile to store the sql statements in.

## Compare

The execution of comparison is done via 

```shell
mysqlcompare compare
```
