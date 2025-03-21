<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['output']) && isset($_POST['feed_name']) && isset($_POST['email_address'])) {
    $html = $_POST['output'];
    $feedName = trim($_POST['feed_name']);
    $emailAddress = trim($_POST['email_address']);
    $fileName = "{$feedName}.xml";

    // Determine dynamic path and URL
    $rssDir = __DIR__ . "/feed/";
    if (!is_dir($rssDir)) {
        mkdir($rssDir, 0755, true);
    }
    $filePath = $rssDir . $fileName;
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $rssFeedUrl = $protocol . "://" . $host . $scriptDir . "/feed/" . $fileName;

    $isNewFile = !file_exists($filePath);

    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Prevent warnings from malformed HTML
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $jobListings = $xpath->query('//section[contains(@class, "card-list-container")]/article');
    $jobs = [];

    foreach ($jobListings as $job) {
        $titleElem = $xpath->query('.//h2[contains(@class, "job-tile-title")]/a', $job);
        $title = $titleElem->length > 0 ? trim($titleElem[0]->textContent) : 'No title found';
        $link = $titleElem->length > 0 ? 'https://www.upwork.com' . $titleElem[0]->getAttribute('href') : 'No link found';

        $descriptionElem = $xpath->query('.//div[contains(@class, "air3-line-clamp-wrapper clamp mb-3")]/div[contains(@class, "air3-line-clamp")]/p[contains(@class, "mb-0 text-body-sm")]', $job);
        $description = $descriptionElem->length > 0 ? trim($descriptionElem[0]->textContent) : 'No description found';

        $publishedDateElem = $xpath->query('.//small[@data-test="job-pubilshed-date"]/span[last()]', $job);
        $publishedDate = $publishedDateElem->length > 0 ? trim($publishedDateElem[0]->textContent) : 'No date found';

        $jobs[] = [
            'title' => $title,
            'link' => $link,
            'description' => $description,
            'publishedDate' => $publishedDate
        ];
    }

    // Create or override RSS feed file
    $rssContent = '<?xml version="1.0" encoding="UTF-8"?>' .
                  '<rss version="2.0">' .
                  '<channel>' .
                  '<title>Upwork Job Feed RSS</title>' .
                  '<description>RSS Feed for Upwork Jobs</description>' .
                  '<link>' . htmlspecialchars($rssFeedUrl) . '</link>';

    foreach ($jobs as $job) {
        $rssContent .= '<item>' .
                       '<title>' . htmlspecialchars($job['title']) . '</title>' .
                       '<link>' . htmlspecialchars($job['link']) . '</link>' .
                       '<description>' . htmlspecialchars($job['description']) . '</description>' .
                       '<pubDate>' . htmlspecialchars($job['publishedDate']) . '</pubDate>' .
                       '</item>';
    }

    $rssContent .= '</channel></rss>';
    file_put_contents($filePath, $rssContent);

    // Send email only if the file is created for the first time
    if ($isNewFile) {
        $subject = 'Your Upwork Job Feed RSS Link';
        $message = "Hello,<br><br>Here is your RSS feed link: <a href='$rssFeedUrl'>$rssFeedUrl</a><br><br>Please add this link to your RSS reader to start receiving job updates.";
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Faraz - The Web Guy <ask@farazthewebguy.com>' . "\r\n";

        if (mail($emailAddress, $subject, $message, $headers)) {
            echo 'Check your email for the RSS feed URL';
        } else {
            echo 'Failed to send email.';
        }
    } else {
        echo 'RSS feed updated successfully.';
    }
} else {
    echo 'Invalid request';
}