<?php
/*
 * Xibo - Digital Signage - http://www.xibo.org.uk
 * Copyright (C) 2015 Spring Signage Ltd
 *
 * This file (ApiStorage.php) is part of Xibo.
 *
 * Xibo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Xibo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Xibo.  If not, see <http://www.gnu.org/licenses/>.
 */


namespace Xibo\Middleware;


use Slim\Middleware;
use Xibo\Helper\Log;
use Xibo\Storage\PDOConnect;

class Storage extends Middleware
{
    public function call()
    {
        $this->app->commit = true;

        $this->next->call();

        // Are we in a transaction coming out of the stack?
        if (PDOConnect::init()->inTransaction()) {
            // We need to commit or rollback? Default is commit
            if ($this->app->commit) {
                PDOConnect::init()->commit();
            } else {

                Log::debug('Storage rollback.');
                PDOConnect::init()->rollBack();
            }
        }

        Log::info('PDO stats: %s.', json_encode(PDOConnect::stats()));

        PDOConnect::close();
    }
}