<?php declare(strict_types=1);

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement\Tests;

class Listener
{
    /**
     * @var list<string>
     */
    private array $data = [];

    /**
     * @var list<string>
     */
    private array $magicData = [];

    /**
     * @var list<string>
     */
    private static array $staticData = [];

    public function onFoo(string $data): void
    {
        $this->data[] = $data;
    }

    public function __invoke(string $data): void
    {
        $this->magicData[] = $data;
    }

    public static function onBar(string $data): void
    {
        self::$staticData[] = $data;
    }

    /**
     * @return list<string>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return list<string>
     */
    public function getMagicData(): array
    {
        return $this->magicData;
    }

    /**
     * @return list<string>
     */
    public static function getStaticData(): array
    {
        return self::$staticData;
    }
}
