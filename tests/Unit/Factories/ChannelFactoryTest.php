<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Channels\EmailChannel;
use App\Channels\TelegramChannel;
use App\Enums\ChannelTypeEnum;
use App\Factories\ChannelFactory;
use Tests\TestCase;

class ChannelFactoryTest extends TestCase
{
    public function test_it_creates_email_channel(): void
    {
        $channel = ChannelFactory::make(ChannelTypeEnum::EMAIL);

        $this->assertInstanceOf(EmailChannel::class, $channel);
    }

    public function test_it_creates_telegram_channel(): void
    {
        $channel = ChannelFactory::make(ChannelTypeEnum::TELEGRAM);

        $this->assertInstanceOf(TelegramChannel::class, $channel);
    }
}
