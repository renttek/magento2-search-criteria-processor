<?php declare(strict_types=1);

use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Logger\Quiet;
use Magento\Framework\DB\Platform\Quote;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\SelectFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\StringUtils;

/** @noinspection PhpIncludeInspection */
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

function getDbConfig(): array
{
    $port = getenv('DB_PORT');

    return [
        'host'     => sprintf('127.0.0.1:%s', $port !== false ? $port : 3306),
        'dbname'   => 'testing',
        'username' => 'root',
        'password' => 'testing',
    ];
}

function getSelectRenderer(): Select\SelectRenderer
{
    $renderers = [
        'distinct'   => [
            'renderer' => new Select\DistinctRenderer,
            'sort'     => 100,
            'part'     => 'distinct',
        ],
        'columns'    => [
            'renderer' => new Select\ColumnsRenderer(new Quote),
            'sort'     => 200,
            'part'     => 'columns',
        ],
        'union'      => [
            'renderer' => new Select\UnionRenderer,
            'sort'     => 300,
            'part'     => 'union',
        ],
        'from'       => [
            'renderer' => new Select\FromRenderer(new Quote),
            'sort'     => 400,
            'part'     => 'from',
        ],
        'where'      => [
            'renderer' => new Select\WhereRenderer,
            'sort'     => 500,
            'part'     => 'where',
        ],
        'group'      => [
            'renderer' => new Select\GroupRenderer(new Quote),
            'sort'     => 600,
            'part'     => 'group',
        ],
        'having'     => [
            'renderer' => new Select\HavingRenderer,
            'sort'     => 700,
            'part'     => 'having',
        ],
        'order'      => [
            'renderer' => new Select\OrderRenderer(new Quote),
            'sort'     => 800,
            'part'     => 'order',
        ],
        'limit'      => [
            'renderer' => new Select\LimitRenderer,
            'sort'     => 900,
            'part'     => 'limitcount',
        ],
        'for_update' => [
            'renderer' => new Select\ForUpdateRenderer,
            'sort'     => 1000,
            'part'     => 'forupdate',
        ],
    ];

    return new Select\SelectRenderer($renderers);
}

function getAdapter(): Mysql
{
    return new Mysql(
        new StringUtils,
        new MagentoDateTime,
        new Quiet(),
        new SelectFactory(getSelectRenderer()),
        getDbConfig(),
        new Json
    );
}
