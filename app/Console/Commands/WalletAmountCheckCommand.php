<?php
/**
 * Copyright (c) 2018-2019 Adshares sp. z o.o.
 *
 * This file is part of AdServer
 *
 * AdServer is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * AdServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AdServer. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types = 1);

namespace Adshares\Adserver\Console\Commands;

use Adshares\Adserver\Mail\WalletFundsEmail;
use Adshares\Adserver\Models\Config;
use Adshares\Adserver\Models\UserLedgerEntry;
use Adshares\Demand\Application\Service\WalletFundsChecker;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Adshares\Adserver\Console\LineFormatterTrait;
use const DATE_ATOM;

class WalletAmountCheckCommand extends Command
{
    use LineFormatterTrait;

    private const SEND_EMAIL_MIN_INTERVAL = 600;

    protected $signature = 'ops:wallet:transfer:check';

    protected $description = 'Check and inform operator about insufficient funds on the account.';

    /** @var WalletFundsChecker */
    private $hotWalletCheckerService;

    public function __construct(WalletFundsChecker $hotWalletCheckerService)
    {
        $this->hotWalletCheckerService = $hotWalletCheckerService;

        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('[Wallet] Start command '.$this->signature);
        $waitingPayments = UserLedgerEntry::waitingPayments();

        $transferValue = $this->hotWalletCheckerService->check($waitingPayments);

        if (!$transferValue) {
            $this->info('[Wallet] No need to transfer clicks from Cold Wallet.');

            return;
        }

        if ($this->shouldEmailBeSent()) {
            $email = config('app.adshares_operator_email');

            Mail::to($email)->queue(
                new WalletFundsEmail($transferValue, (string)config('app.adshares_address'))
            );

            $message = sprintf(
                '[Wallet] Email has been sent to %s to transfer %s clicks from Cold (%s) to Hot Wallet (%s).',
                $email,
                $transferValue,
                config('app.adshares_wallet_cold_address'),
                config('app.adshares_address')
            );

            Config::updateDateTimeByKey(Config::OPERATOR_WALLET_EMAIL_LAST_TIME, new DateTime());

            $this->info($message);

            return;
        }

        $this->info('[Wallet] Email does not need to be sent because we sent it a few minutes before.');
    }

    private function shouldEmailBeSent(): bool
    {
        $now = new DateTime();
        $date = Config::fetch(Config::OPERATOR_WALLET_EMAIL_LAST_TIME);

        if (!$date) {
            return true;
        }

        $lastEmailTime = DateTime::createFromFormat(DATE_ATOM, $date);
        $dateUntilEmailIsSent = $lastEmailTime->modify(sprintf('%d second', self::SEND_EMAIL_MIN_INTERVAL));

        return $dateUntilEmailIsSent < $now;
    }
}