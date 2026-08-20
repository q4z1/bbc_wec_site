<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $entry)
    <url>
        <loc>{{ $entry['loc'] }}</loc>
@if(!empty($entry['lastmod']))
        <lastmod>{{ $entry['lastmod'] }}</lastmod>
@endif
        <changefreq>{{ $entry['changefreq'] }}</changefreq>
        <priority>{{ $entry['priority'] }}</priority>
    </url>
@endforeach
</urlset>
