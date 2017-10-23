# 1337x-API

Parse 1337x torrent site to get Movies & Tv Shows torrents with their information.

## Steps

Process is divided into 3 steps.

1. Search for torrents by Movie or TV title. Fetch titles from imdb lists database (more on that later). Save HTML results pages.
2. Parse results pages and save HTML page for each torrent found.
3. Parse torrent page to collect required info: Details(Category, Quality, Language, etc. ), hash, download links, screenshots ...

