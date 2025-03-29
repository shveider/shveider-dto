<?php

declare(strict_types=1);

namespace ShveiderDto;

interface Transferable
{
    public function transfer(): DataTransferObjectInterface;
}
