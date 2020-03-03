<?php

/** To be replaced with real sendRabbit() that actually gets sends data to/from rabbit.
 * The $data parameter is assumed to always contain a key named 'action'.
 * The key could be used by rabbit to determine the kind of data we're looking for
 */
function sRabbit($data) {
    $sampleShows = [
        [
            'id' => 1,
            'name' => 'Show One',
            'genre' => 'Genre One',
            'upcoming_episodes' => [
                ['day' => 'Monday', 'date' => '2020-03-20', 'time' => '10:00:00', 'channel' => 'Network One'],
                ['day' => 'Tuesday', 'date' => '2020-03-21', 'time' => '10:00:00', 'channel' => 'Network Two'],
                ['day' => 'Wednesday', 'date' => '2020-03-22', 'time' => '10:00:00', 'channel' => 'Network Three']
            ],
            'poster_graphic' => 'https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg',
        ], [
            'id' => 2,
            'name' => 'Show Two',
            'genre' => 'Genre One',
            'upcoming_episodes' => [
                ['day' => 'Monday', 'date' => '2020-03-20', 'time' => '10:00:00', 'channel' => 'Network One'],
                ['day' => 'Tuesday', 'date' => '2020-03-21', 'time' => '10:00:00', 'channel' => 'Network Two'],
                ['day' => 'Wednesday', 'date' => '2020-03-22', 'time' => '10:00:00', 'channel' => 'Network Three']
            ],
            'poster_graphic' => 'https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg',
        ], [
            'id' => 3,
            'name' => 'Show Three',
            'genre' => 'Genre One',
            'upcoming_episodes' => [
                ['day' => 'Monday', 'date' => '2020-03-20', 'time' => '10:00:00', 'channel' => 'Network One'],
                ['day' => 'Tuesday', 'date' => '2020-03-21', 'time' => '10:00:00', 'channel' => 'Network Two'],
                ['day' => 'Wednesday', 'date' => '2020-03-22', 'time' => '10:00:00', 'channel' => 'Network Three']
            ],
            'poster_graphic' => 'https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg',
        ],
    ];

    $sampleSchedule = [
        ['day' => 'Monday', 'date' => '2020-03-16', 'shows' => array_slice($sampleShows, 0, mt_rand(0, 3))],
        ['day' => 'Tuesday', 'date' => '2020-03-17', 'shows' => array_slice($sampleShows, 0, mt_rand(0, 3))],
        ['day' => 'Wednesday', 'date' => '2020-03-18', 'shows' => array_slice($sampleShows, 0, mt_rand(0, 3))],
        ['day' => 'Thursday', 'date' => '2020-03-19', 'shows' => array_slice($sampleShows, 0, mt_rand(0, 3))],
        ['day' => 'Friday', 'date' => '2020-03-20', 'shows' => array_slice($sampleShows, 0, mt_rand(0, 3))],
        ['day' => 'Saturday', 'date' => '2020-03-21', 'shows' => array_slice($sampleShows, 0, mt_rand(0, 3))],
        ['day' => 'Sunday', 'date' => '2020-03-22', 'shows' => array_slice($sampleShows, 0, mt_rand(0, 3))],
    ];

    if($data['action'] == 'get_show')
        foreach ($sampleShows as $sampleShow)
            if($sampleShow['id'] == $data['show_id']) return $sampleShow;

    if($data['action'] == 'get_shows')
        return $sampleShows;

    if($data['action'] == 'like_show')
        return ['success' => true];

    if($data['action'] == 'follow_show') {
        // Send email of show's upcoming episode
        foreach ($sampleShows as $sampleShow) {
            if ($sampleShow['id'] == $data['show_id']) {
                mail('example@example.com', 'You Followed a Show. ', 'You follow ' . $sampleShow['name'] . '. It will be airing on ' . $sampleShow['upcoming_episodes'][0]['date'] . ', ' . $sampleShow['upcoming_episodes'][0]['day'] . ', ' . $sampleShow['upcoming_episodes'][0]['time'] . ', and on ' . $sampleShow['upcoming_episodes'][0]['channel'] . '.');
            }
        }

        return ['success' => true];
    }

    if($data['action'] == 'get_schedule')
        return $sampleSchedule;

}