<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by COQPIT on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace COQPIT\Core\Vendor\Symfony\Component\VarDumper\Cloner;

use COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\Caster;
use COQPIT\Core\Vendor\Symfony\Component\VarDumper\Exception\ThrowingCasterException;

/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = [
        '__PHP_Incomplete_Class' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\Caster', 'castPhpIncompleteClass'],

        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\CutStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\CutArrayStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'castCutArray'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ConstStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\EnumStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'castEnum'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ScalarStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'castScalar'],

        'Fiber' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\FiberCaster', 'castFiber'],

        'Closure' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClosure'],
        'Generator' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castGenerator'],
        'ReflectionType' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castType'],
        'ReflectionAttribute' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castAttribute'],
        'ReflectionGenerator' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReflectionGenerator'],
        'ReflectionClass' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClass'],
        'ReflectionClassConstant' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClassConstant'],
        'ReflectionFunctionAbstract' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castFunctionAbstract'],
        'ReflectionMethod' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castMethod'],
        'ReflectionParameter' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castParameter'],
        'ReflectionProperty' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castProperty'],
        'ReflectionReference' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReference'],
        'ReflectionExtension' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castExtension'],
        'ReflectionZendExtension' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castZendExtension'],

        'Doctrine\Common\Persistence\ObjectManager' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Doctrine\Common\Proxy\Proxy' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castCommonProxy'],
        'Doctrine\ORM\Proxy\Proxy' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castOrmProxy'],
        'Doctrine\ORM\PersistentCollection' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castPersistentCollection'],
        'Doctrine\Persistence\ObjectManager' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],

        'DOMException' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'],
        'DOMStringList' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNameList' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMImplementation' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'],
        'DOMImplementationList' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNode' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castNode'],
        'DOMNameSpaceNode' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castNameSpaceNode'],
        'DOMDocument' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDocument'],
        'DOMNodeList' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNamedNodeMap' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMCharacterData' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castCharacterData'],
        'DOMAttr' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castAttr'],
        'DOMElement' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castElement'],
        'DOMText' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castText'],
        'DOMDocumentType' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDocumentType'],
        'DOMNotation' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castNotation'],
        'DOMEntity' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castEntity'],
        'DOMProcessingInstruction' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castProcessingInstruction'],
        'DOMXPath' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DOMCaster', 'castXPath'],

        'XMLReader' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\XmlReaderCaster', 'castXmlReader'],

        'ErrorException' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castErrorException'],
        'Exception' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castException'],
        'Error' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castError'],
        'Symfony\Bridge\Monolog\Logger' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\DependencyInjection\ContainerInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\EventDispatcher\EventDispatcherInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\HttpClient\AmpHttpClient' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'Symfony\Component\HttpClient\CurlHttpClient' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'Symfony\Component\HttpClient\NativeHttpClient' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'Symfony\Component\HttpClient\Response\AmpResponse' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpClient\Response\CurlResponse' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpClient\Response\NativeResponse' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'Symfony\Component\HttpFoundation\Request' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castRequest'],
        'Symfony\Component\Uid\Ulid' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUlid'],
        'Symfony\Component\Uid\Uuid' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUuid'],
        'Symfony\Component\VarExporter\Internal\LazyObjectState' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castLazyObjectState'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Exception\ThrowingCasterException' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castThrowingCasterException'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\TraceStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castTraceStub'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\FrameStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFrameStub'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Cloner\AbstractCloner' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\ErrorHandler\Exception\FlattenException' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFlattenException'],
        'Symfony\Component\ErrorHandler\Exception\SilencedErrorContext' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castSilencedErrorContext'],

        'Imagine\Image\ImageInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ImagineCaster', 'castImage'],

        'Ramsey\Uuid\UuidInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\UuidCaster', 'castRamseyUuid'],

        'ProxyManager\Proxy\ProxyInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ProxyManagerCaster', 'castProxy'],
        'PHPUnit_Framework_MockObject_MockObject' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\MockObject' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\Stub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Prophecy\Prophecy\ProphecySubjectInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Mockery\MockInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],

        'PDO' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdo'],
        'PDOStatement' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdoStatement'],

        'AMQPConnection' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castConnection'],
        'AMQPChannel' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castChannel'],
        'AMQPQueue' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castQueue'],
        'AMQPExchange' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castExchange'],
        'AMQPEnvelope' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castEnvelope'],

        'ArrayObject' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayObject'],
        'ArrayIterator' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayIterator'],
        'SplDoublyLinkedList' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castDoublyLinkedList'],
        'SplFileInfo' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileInfo'],
        'SplFileObject' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileObject'],
        'SplHeap' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'SplObjectStorage' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castObjectStorage'],
        'SplPriorityQueue' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'OuterIterator' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castOuterIterator'],
        'WeakMap' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakMap'],
        'WeakReference' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakReference'],

        'Redis' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'],
        'Relay\Relay' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'],
        'RedisArray' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisArray'],
        'RedisCluster' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisCluster'],

        'DateTimeInterface' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DateCaster', 'castDateTime'],
        'DateInterval' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DateCaster', 'castInterval'],
        'DateTimeZone' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DateCaster', 'castTimeZone'],
        'DatePeriod' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DateCaster', 'castPeriod'],

        'GMP' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\GmpCaster', 'castGmp'],

        'MessageFormatter' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\IntlCaster', 'castMessageFormatter'],
        'NumberFormatter' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\IntlCaster', 'castNumberFormatter'],
        'IntlTimeZone' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlTimeZone'],
        'IntlCalendar' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlCalendar'],
        'IntlDateFormatter' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlDateFormatter'],

        'Memcached' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\MemcachedCaster', 'castMemcached'],

        'Ds\Collection' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DsCaster', 'castCollection'],
        'Ds\Map' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DsCaster', 'castMap'],
        'Ds\Pair' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DsCaster', 'castPair'],
        'COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DsPairStub' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\DsCaster', 'castPairStub'],

        'mysqli_driver' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\MysqliCaster', 'castMysqliDriver'],

        'CurlHandle' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castCurl'],

        ':dba' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'],
        ':dba persistent' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'],

        'GdImage' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castGd'],
        ':gd' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castGd'],

        ':pgsql large object' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLargeObject'],
        ':pgsql link' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql link persistent' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql result' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castResult'],
        ':process' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castProcess'],
        ':stream' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'],

        'OpenSSLCertificate' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castOpensslX509'],
        ':OpenSSL X.509' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castOpensslX509'],

        ':persistent stream' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'],
        ':stream-context' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStreamContext'],

        'XmlParser' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'],
        ':xml' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'],

        'RdKafka' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castRdKafka'],
        'RdKafka\Conf' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castConf'],
        'RdKafka\KafkaConsumer' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castKafkaConsumer'],
        'RdKafka\Metadata\Broker' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castBrokerMetadata'],
        'RdKafka\Metadata\Collection' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castCollectionMetadata'],
        'RdKafka\Metadata\Partition' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castPartitionMetadata'],
        'RdKafka\Metadata\Topic' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicMetadata'],
        'RdKafka\Message' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castMessage'],
        'RdKafka\Topic' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopic'],
        'RdKafka\TopicPartition' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicPartition'],
        'RdKafka\TopicConf' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicConf'],

        'FFI\CData' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData'],
        'FFI\CType' => ['COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData'],
    ];

    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;

    /**
     * @var array<string, list<callable>>
     */
    private array $casters = [];

    /**
     * @var callable|null
     */
    private $prevErrorHandler;

    private array $classInfo = [];
    private int $filter = 0;

    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(?array $casters = null)
    {
        $this->addCasters($casters ?? static::$defaultCasters);
    }

    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     *
     * @return void
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }

    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     *
     * @return void
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }

    /**
     * Sets the maximum cloned length for strings.
     *
     * @return void
     */
    public function setMaxString(int $maxString)
    {
        $this->maxString = $maxString;
    }

    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     *
     * @return void
     */
    public function setMinDepth(int $minDepth)
    {
        $this->minDepth = $minDepth;
    }

    /**
     * Clones a PHP variable.
     *
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     */
    public function cloneVar(mixed $var, int $filter = 0): Data
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }

            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }

            return false;
        });
        $this->filter = $filter;

        if ($gc = gc_enabled()) {
            gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                gc_enable();
            }
            restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }

    /**
     * Effectively clones the PHP variable.
     */
    abstract protected function doClone(mixed $var): array;

    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castObject(Stub $stub, bool $isNested): array
    {
        $obj = $stub->value;
        $class = $stub->class;

        if (str_contains($class, "@anonymous\0")) {
            $stub->class = get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = method_exists($class, '__debugInfo');

            foreach (class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';

            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : [
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];

            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }

        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);

        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }

    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castResource(Stub $stub, bool $isNested): array
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;

        try {
            if (!empty($this->casters[':'.$type])) {
                foreach ($this->casters[':'.$type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }
}
