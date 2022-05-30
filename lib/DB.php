<?php
namespace lib;

class DB {
    private $connection;

    public function __construct($host, $database, $user, $password)
    {
        $this->connection = new \PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $password);
    }

    public function add(array $data)
    {
        if (!isset($data['pay_address'])) {
            $data['pay_address'] = '';
        }

        $st = $this->connection->prepare(
            "INSERT INTO tokens (address, pay_address, content, status, error, crc32) VALUES(?, ?, ?, ?, ?, ?)");
        return $st->execute([$data['address'], $data['pay_address'], $data['content'], $data['status'], $data['error'], $data['crc32']]);
    }

    public function updateError(string $address, string $pay_address, string $error)
    {
        $st = $this->connection->prepare(
            "UPDATE tokens SET status = 'ERROR', `error` = ? WHERE address = ?");
        return $st->execute([$error, $address]);
    }

    public function updateStatus(string $address, string $pay_address, string $status)
    {
        $st = $this->connection->prepare(
            "UPDATE tokens SET status = ? WHERE address = ?");

        return $st->execute([$status, $address]);
    }

    public function update(string $address, string $pay_address, array $data)
    {
        $st = $this->connection->prepare(
            "UPDATE tokens SET content = ?, pay_address = ?, `error` = ?, crc32 = ?, `status` = ? WHERE address = ?");

        return $st->execute([$data['content'], $data['pay_address'], $data['error'], $data['crc32'], $data['status'], $address]);
    }

    public function check(string $address, string $pay_address, int $hours = 0)
    {
        $st = $this->connection->prepare(
            "UPDATE tokens SET status = 'CHECKED', hours = ? WHERE address = ?");

        return $st->execute([$hours, $address]);
    }

    public function activate(string $address, string $pay_address, string $gen_addr, float $hours)
    {
        $st = $this->connection->prepare(
            "UPDATE tokens SET status = 'ACTIVATED', gen_address = ?, `hours` = ? WHERE address = ?");

        return $st->execute([$gen_addr, $hours, $address]);
    }

    public function pay(string $address, string $pay_address, float $hours)
    {
        $st = $this->connection->prepare(
            "UPDATE tokens SET status = 'PAYED', `hours` = ? WHERE address = ?");

        return $st->execute([$hours, $address]);
    }

    public function find(string $address, string $pay_address)
    {
        $st = $this->connection->prepare("SELECT * FROM tokens WHERE address = ?");
        $st->execute([$address]);
        $rows = $st->fetchAll();
        if ($st->rowCount() >= 1) {
            return $rows[0];
        } else {
            return false;
        }
    }
    
    public function findAll()
    {
        $st = $this->connection->query("SELECT * FROM tokens ORDER BY ID DESC", \PDO::FETCH_ASSOC);
        return $st->fetchAll();
    }
}