<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Amqp\Connection;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Create connection based on options.
 */
class Factory
{
    /**
     * Create connection according to given options.
     *
     * @param FactoryOptions $options
     *
     * @return AbstractConnection
     */
    public function create(FactoryOptions $options): AbstractConnection
    {
        if ($options->isSslEnabled()) {
            return new AMQPSSLConnection(
                $options->getHost(),
                $options->getPort(),
                $options->getUsername(),
                $options->getPassword(),
                $options->getVirtualHost() !== null
                    ? $options->getVirtualHost() : '/',
                //Note: when you are passing empty array of SSL options
                //PHP-AMQPLIB will actually create an un-secure connection.
                $options->getSslOptions() !== null
                    ? $options->getSslOptions() : ['verify_peer' => true]
            );
        } else {
            return new AMQPStreamConnection(
                $options->getHost(),
                $options->getPort(),
                $options->getUsername(),
                $options->getPassword(),
                $options->getVirtualHost() !== null
                    ? $options->getVirtualHost() : '/'
            );
        }
    }
}
