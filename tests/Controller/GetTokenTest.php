<?php

/*
 * This file is part of the CoopTilleulsForgotPasswordBundle package.
 *
 * (c) Vincent CHALAMON <vincent@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CoopTilleuls\ForgotPasswordBundle\Tests\Controller;

use CoopTilleuls\ForgotPasswordBundle\Controller\GetToken;
use CoopTilleuls\ForgotPasswordBundle\Entity\AbstractPasswordToken;
use CoopTilleuls\ForgotPasswordBundle\Normalizer\NormalizerInterface;
use CoopTilleuls\ForgotPasswordBundle\Provider\ProviderInterface;
use CoopTilleuls\ForgotPasswordBundle\Tests\ProphecyTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Vincent CHALAMON <vincentchalamon@gmail.com>
 */
final class GetTokenTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ProviderInterface|ObjectProphecy
     */
    private $providerMock;

    /**
     * @var NormalizerInterface|ObjectProphecy
     */
    private $normalizerMock;

    /**
     * @var AbstractPasswordToken|ObjectProphecy
     */
    private $tokenMock;

    protected function setUp(): void
    {
        $this->providerMock = $this->prophesize(ProviderInterface::class);
        $this->normalizerMock = $this->prophesize(NormalizerInterface::class);
        $this->tokenMock = $this->prophesize(AbstractPasswordToken::class);
    }

    public function testGetTokenAction(): void
    {
        $this->providerMock->getPasswordTokenSerializationGroups()
            ->willReturn(['foo'])
            ->shouldBeCalledOnce();
        $this->normalizerMock->normalize($this->tokenMock->reveal(), 'json', ['groups' => ['foo']])
            ->willReturn(['foo' => 'bar'])
            ->shouldBeCalledOnce();
        $controller = new GetToken($this->normalizerMock->reveal());
        $response = $controller($this->tokenMock->reveal(), $this->providerMock->reveal());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(json_encode(['foo' => 'bar']), $response->getContent());
    }
}
