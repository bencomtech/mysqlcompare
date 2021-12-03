<?php namespace LLoadout\MysqlCompare;

use Illuminate\Support\Collection;
use function Termwind\{render};

class DatabaseManager
{

    public function getSchema($connectionName)
    {
        if (!isset($this->{$connectionName . "Schema"})) {
            $this->{$connectionName . "Schema"} = $this->getSchemaManager($connectionName)->createSchema();
        }
        return $this->{$connectionName . "Schema"};
    }

    public function getSchemaManager($connectionName,)
    {
        $connection = $this->getConnection($connectionName);
        render('<div class="p-1 ml-1 bg-green-800 text-white">Fetching ' . $connectionName . ' schema</div>');
        render('<br>');
        return $connection->getSchemaManager();
    }

    public function getConnection($connectionName)
    {
        if (!isset($this->{$connectionName . "Connection"})) {

            $connection = config($connectionName);
            if (isset($connection['ssh']) && !empty($connection['ssh'])) {
                $ports = ['source' => 3333, 'target' => 3334];
                render('<div class="p-1 ml-1 bg-green-800 text-white">Establishing ' . $connectionName . ' connection over ssh 🔐</div>');
                $connection['port'] = $ports[$connectionName];
                exec('ssh -f -L ' . $connection['port'] . ':127.0.0.1:3306 ' . $connection['ssh'] . ' sleep 10 > /dev/null');
            } else {
                render('<div class="p-1 ml-1 bg-green-800 text-white">Establishing ' . $connectionName . ' connection</div>');
            }

            $this->{$connectionName . "Connection"} = \Doctrine\DBAL\DriverManager::getConnection($connection);
            $databasePlatform                       = $this->{$connectionName . "Connection"}->getDatabasePlatform();
            $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
        }
        return $this->{$connectionName . "Connection"};

    }


    public function compare(): self
    {
        render('<div class="m-1 p-4 font-bold bg-green-800">mysqlcompare</div>');
        $sourceSchema = $this->getSchema('source');
        $targetSchema = $this->getSchema('target');
        render('<div class="p-1 ml-1 bg-yellow-800 text-white">Getting difference</div>');

        return $this->getDifference($targetSchema, $sourceSchema);
    }

    public function getSql(): string
    {
        return $this->getStatements()->implode(';' . PHP_EOL) . ";";
    }

    public function exec(): void
    {
        $this->getStatements()->each(function ($sql) {
            $this->getConnection('target')->statement($sql);
        });
    }

    public function saveToFile(): void
    {
        $filename = config('sqlfile');
        render('<br>');
        render('<div class="p-1 ml-1 bg-green-800 text-white">Writing sql statements to ' . $filename . ' 🥳</div>');
        render('<br>');
        $file = fopen($filename, "w") or die("Unable to open file!");
        fwrite($file, $this->getSql());
        fclose($file);
    }

    private function getStatements(): Collection
    {
        $databasePlatform = $this->getConnection('target')->getDatabasePlatform();
        $statements       = collect($this->schemaDiff->toSaveSql($databasePlatform));

        return $statements->merge($this->getDropStatements());
    }

    /**
     * @param $sourceSchema <string>
     * @param $targetSchema <string>
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    private function getDifference($targetSchema, $sourceSchema): DatabaseManager
    {
        $comparator       = new \Doctrine\DBAL\Schema\Comparator();
        $this->schemaDiff = $comparator->compare($targetSchema, $sourceSchema);

        return $this;
    }

    public function hasDifference(): bool
    {
        return (bool)$this->getStatements()->count();
    }

    private function getDropStatements(): Collection
    {
        $dropStatements = collect();
        $sourceTables   = $this->getTables($this->getSchema('source'));
        $targetTables   = $this->getTables($this->getSchema('target'));
        $targetTables->diff($sourceTables)->each(function ($table) use (&$dropStatements) {
            $dropStatements->push("DROP TABLE $table");
        });

        return $dropStatements;
    }

    private function getTables($sourceSchema): Collection
    {
        return collect($sourceSchema->getTables())->transform(fn($table) => $table->getName())->values();
    }
}
