<?php

class CustomFLVFormat extends FFMpeg\Format\Video\DefaultVideo
{
    public function __construct($audioCodec = 'mp3', $videoCodec = 'flv1')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    public function supportBFrames()
    {
        return false;
    }

    public function getAvailableAudioCodecs()
    {
        return array('mp3');
    }

    public function getAvailableVideoCodecs()
    {
        return array('flv1');
    }
}

class Custom3GPFormat extends FFMpeg\Format\Video\DefaultVideo
{
    public function __construct($audioCodec = 'aac', $videoCodec = 'h263')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    public function supportBFrames()
    {
        return false;
    }

    public function getAvailableAudioCodecs()
    {
        return array('aac');
    }

    public function getAvailableVideoCodecs()
    {
        return array('h263');
    }
}

?>