<?php

namespace DorsetDigital\EdgePublisher;

interface EdgePublisher
{
    public function savePage($url, $content);

    public function deletePage($url);
}