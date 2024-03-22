<?php 
/**
 * One of the best applications of the Builder pattern is an SQL query builder. The builder
 * interface defines the common steps required to build a generic SQL query. On the other
 * hand, concrete builders, corresponding to different SQL dialects, implement these steps
 * by returning parts of SQL queries that can be executed in a particular database engine.
 */
namespace RefactoringGuru\Creational\Builder\RealWorld;

/**
 * The Builder interface declares a set of methods to assemble an SQL query.
 * 
 * All of the constructino steps are returning the current builder object to
 * allow chaining: $builder->select(...)->where(...)
 */
interface SQLQueryBuilder 
{
    public function select(string $table, array $fields): SQLQueryBuilder;
    public function where(string $field, string $value, string $operator = '='): SQLQueryBuilder;
    public function limit(int $start, int $offset): SQLQueryBuilder;

    // many other SQL syntax methods here ......

    public function getSQL(): string;
}

/**
 * Each Concrete Builder corresponds to a specific SQL dialect and may implement
 * the builder steps a little bit differently from the others.
 * 
 * This Concrete Builder can build SQL queries compatible with MySQL.
 */
class MysqlQueryBuilder implements SQLQueryBuilder 
{
    protected $query;

    protected function reset(): void 
    {
        $this->query = new \stdClass();
    }

    /**
     * Build a base SELECT query.
     */
    public function select(string $table, array $fields): SQLQueryBuilder 
    {
        $this->reset();
        $this->query->base = "SELECT " . implode(", ", $fields) . " FROM " . $table;
        $this->query->type = 'select';

        return $this;
    }

    /**
     * Add a WHERE condition.
     */
    public function where(string $field, string $value, string $operator = '='): SQLQueryBuilder 
    {
        if (!in_array($this->query->type, ['select', 'update', 'delete'])) {
            throw new \Exception("WHERE can only be added to SELECT, UPDATE or DELETE");
        }

        $this->query->where[] = "$field $operator '$value'";

        return $this;
    }

    /**
     * Add a LIMIT constraint.
     */
    public function limit(int $start, int $offset): SQLQueryBuilder 
    {
        if (!in_array($this->query->type, ['select'])) {
            throw new \Exception("LIMIT can only be added to SELECT");
        }

        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

    /**
     * Get the final query string.
     */
    public function getSQL(): string 
    {
        $query = $this->query;
        $sql = $query->base;
        if (!empty($query->where)) {
            $sql .= " WHERE " . implode(' AND ', $query->where);
        }
        if (isset($query->limit)) {
            $sql .= $query->limit;
        }
        $sql .= ";";

        return $sql;
    }
}

    /**
     * This Concrete Builder is compatible with PostgreSQL. While Postgres is very
     * similar to MySQL, it still has several differences. To reuse the common code,
     * we extend it from the MySQL builder, while overriding some of the building
     * steps.
     */
    class PostgresQueryBuilder extends MysqlQueryBuilder 
    {
        /**
         * Among other things, PostgreSQL has slightly different LIMIT syntax.
         */
        public function limit(int $start, int $offset): SQLQueryBuilder 
        {
            parent::limit($start, $offset);

            $this->query->limit = " LIMIT " . $start . " OFFSET " . $offset;

            return $this;
        }

        // ...... tons of other overrides here ......
}

/**
 * Note that the client code uses the builder object directly. A designated\
 * Director class is not necessary in this case, because the client code needs
 * different queries almost every time, so the sequence of the construction
 * steps cannot be easily reused.
 * 
 * Since all our query builders create products of the same type (which is a
 * string), we can interact with all builders using their common interface.
 * Later, if we implement a new Builder class, we will be able to pass its
 * instance to the existing client code without breaking it thanks to the
 * SQLQueryBuilder interface.
 */
function clientCode(SQLQueryBuilder $queryBuilder) 
{
    // ......
    $query = $queryBuilder
        ->select("users", ["name", "email", "password"])
        ->where("age", 18, ">")
        ->where("age", 30, "<")
        ->limit(10, 20)
        ->getSQL();
    
    echo $query;

    // ......
}

/**
 * The application selects the proper builder type depending on a current
 * configuratino or the environment settings.
 */

//  if ($_ENV['database_type'] == 'postgres') {
//     $builder = new PostgresQueryBuilder();
//  } else {
//     $builder = new MysqlQueryBuilder();
//  }

echo "Testing MySQL query builder:" . PHP_EOL;
clientCode(new MysqlQueryBuilder());

echo PHP_EOL . PHP_EOL;

echo "Testing PostgresSQL query builder:" . PHP_EOL;
clientCode(new PostgresQueryBuilder());

echo PHP_EOL;

exit(0);
