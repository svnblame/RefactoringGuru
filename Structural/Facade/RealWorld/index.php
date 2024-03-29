<?php

namespace RefactoringGuru\Structural\Facade\RealWorld;

/**
 * The Facade provides a single method for downloading videos from YouTube. This
 * method hides all the complexity of the PHP network layer, YouTube API and the
 * video conversion library (FFmpeg).
 */
class YouTubeDownloader
{
    protected $youtube;
    protected $ffmpeg;

    /**
     * It is handy when the Facade can manage the lifecycle of the subsystem
     * it uses.
     */
    public function __construct(string $youtubeApiKey)
    {
        $this->youtube = new YouTube($youtubeApiKey);
        $this->ffmpeg  = new FFMpeg();
    }

    /**
     * The Facade provides a simple method for downloading video and encoding it
     * to a target format (for the sake of simplicity, the real-world code is
     * commented-out).
     */
    public function downloadVideo(string $url): void
    {
        echo "Fetching video metadata from youtube..." . PHP_EOL;
        // $title = $this->youtube->fetchVideo($url)->getTitle();
        echo "Saving video file to a temporary file..." . PHP_EOL;
        // $this->youtube->saveAs($url, "video.mpg");

        echo "Processing source video..." . PHP_EOL;
        // $video = $this->ffmeg->open('video.mpg');
        echo "Normalizing and resizing the video to smaller dimensions..." . PHP_EOL;
        // $video->filters()->resize(new FFMpeg\Coordinate\Dimension(320, 240))->synchronize();
        echo "Capturing preview image..." . PHP_EOL;
        // $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10)->save($title . 'frame.jpg');
        echo "Saving video in target formats..." . PHP_EOL;
        // $video->save(new FFMpeg\Format\Video\X264(), $title . '.mp4')
        //       ->save(new FFMpeg\Format\Video\WMV(), $title . 'wmv')
        //       ->save(new FFMpeg\Format\Video\WebM(), $title . 'webm');
        echo "Done!" . PHP_EOL;
    }
}

/**
 * The YouTube API subsystem.
 */
class YouTube
{
    public function fetchVideo(): string { return 'some string...'; }
    public function saveAs(string $path): void { /* ... */ }

    // ... more methods and classes ...
}

/**
 * The FFMpeg subsystem ( a complex video/audio conversion library ).
 */
class FFMpeg
{
    public static function create(): FFMpeg { return new FFMpeg(); } // << Fake code...
    public function open(string $video): void { /* ... */ }

    // ... more methods and classes ...
}

class FFMpegVideo
{
    public function filters(): self { return $this; }
    public function resize(): self { return $this; }
    public function synchronize(): self { return $this; }
    public function frame(): self { return $this; }
    public function save(string $path): self { return $this; }

    // ... more methods and classes ...

}

/**
 * The client code does not depend on any subsystem's classes. Any changes
 * inside the subsystem's code won't affect the client code. You will only need
 * to update the Facade.
 */
function clientCode(YouTubeDownloader $facade)
{
    // ...

    $facade->downloadVideo("https://www.youtube.com/watch?v=QH2-TGUlwu4");

    // ...
}

$facade = new YouTubeDownloader("APIKEY-XXXXXXXXX");
clientCode($facade);