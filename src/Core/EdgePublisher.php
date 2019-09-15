<?php

namespace DorsetDigital\EdgePublisher\Core;

interface EdgePublisher
{
    public function savePage($url, $content);

    public function deletePage($url);
}