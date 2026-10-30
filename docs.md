# Viewlix Mobile App API Documentation (v1)

Welcome to the **Viewlix Mobile API** documentation. This RESTful API provides endpoints for authentication, media browsing (Movies, TV Shows, Anime), dynamic streaming/embed sources, user watchlist, cross-platform watch history synchronization, and in-app support chat.

---

## Table of Contents
1. [General Information](#general-information)
2. [Standard Response Envelope](#standard-response-envelope)
3. [Authentication & Profile](#1-authentication--profile)
4. [App Configuration & Updates](#2-app-configuration--updates)
5. [Home Feed](#3-home-feed)
6. [Movies](#4-movies)
7. [TV Shows & Seasons](#5-tv-shows--seasons)
8. [Anime](#6-anime)
9. [Search, Discovery & Providers](#7-search-discovery--providers)
10. [Favorites & Watchlist](#8-favorites--watchlist)
11. [Watch History & Realtime Sync](#9-watch-history--realtime-sync)
12. [In-App Support & Chat](#10-in-app-support--chat)
13. [Mobile Implementation Examples](#11-mobile-implementation-examples)

---

## General Information

- **Base URL**: `https://your-domain.com/api/v1` (or `http://127.0.0.1:8000/api/v1` in development)
- **Content-Type**: `application/json`
- **Accept**: `application/json`
- **Authentication**: Bearer Token via HTTP header:
  ```http
  Authorization: Bearer <SANCTUM_TOKEN>
  ```

---

## Standard Response Envelope

### Success Response
```json
{
  "success": true,
  "message": "Success",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Invalid email or password credentials.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Common HTTP Status Codes
| Status | Meaning |
| :--- | :--- |
| `200 OK` | Request succeeded. |
| `201 Created` | Resource created successfully. |
| `400 Bad Request` | Malformed request or missing parameters. |
| `401 Unauthorized` | Missing or invalid `Bearer` token. |
| `403 Forbidden` | Authenticated user lacks permission. |
| `404 Not Found` | Requested media, ticket, or resource does not exist. |
| `422 Unprocessable` | Input validation failed. Detailed errors in `errors` map. |
| `500 Server Error` | Unexpected backend or third-party service exception. |

---

## 1. Authentication & Profile

### `POST /auth/register`
Create a new user account and obtain a Sanctum Bearer token.

**Request Body:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "SecretPassword123!",
  "password_confirmation": "SecretPassword123!",
  "device_name": "Google Pixel 8"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "token": "1|AbCdEf123456...",
    "token_type": "Bearer",
    "user": {
      "id": 12,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "avatar": null,
      "avatar_url": null,
      "firebase_uuid": "c76a9be8-d218-4a69-a1b7-a859e9a4f48b",
      "is_admin": false,
      "settings": {},
      "created_at": "2026-10-30T12:00:00Z"
    }
  }
}
```

---

### `POST /auth/login`
Authenticate using email and password.

**Request Body:**
```json
{
  "email": "jane@example.com",
  "password": "SecretPassword123!",
  "device_name": "iPhone 15 Pro"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "2|XyZaBc987654...",
    "token_type": "Bearer",
    "user": {
      "id": 12,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "avatar_url": "https://cdn.viewlix.online/avatars/avatar_12.png",
      "firebase_uuid": "c76a9be8-d218-4a69-a1b7-a859e9a4f48b",
      "is_admin": false
    }
  }
}
```

---

### `POST /auth/google` (Android & Mobile Google Sign-In)
Unified endpoint to login or register a user via Google Sign-In on Android.
- If the Google account has not signed up before, it **registers** a new account (email marked verified automatically).
- If the user already exists (matches `google_id` or existing `email`), it links the Google ID and **logs them in**.
- Returns a Sanctum Bearer token and an optional Firebase custom token for instant RTDB sync.

**Request Body:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6Ij...",
  "device_name": "Google Pixel 8"
}
```
*Note: Also accepts `token` or `access_token`.*

**Response (200 OK for login, 201 Created for register):**
```json
{
  "success": true,
  "message": "Login with Google successful",
  "data": {
    "token": "2|AbCdEf123456...",
    "token_type": "Bearer",
    "is_new_user": false,
    "firebase_token": "eyJhbGciOiJSUzI1NiIs...",
    "user": {
      "id": 12,
      "name": "Jane Doe",
      "email": "jane@gmail.com",
      "avatar": "https://lh3.googleusercontent.com/a/...",
      "avatar_url": "https://lh3.googleusercontent.com/a/...",
      "firebase_uuid": "c76a9be8-d218-4a69-a1b7-a859e9a4f48b",
      "is_admin": false,
      "settings": {},
      "created_at": "2026-10-30T12:00:00Z"
    }
  }
}
```

---

### `POST /auth/google/link` *(Auth Required)*
Link a Google account to an already logged-in user profile.

**Request Body:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6Ij..."
}
```

---

### `GET /auth/me` *(Auth Required)*
Retrieve profile details and library stats for the logged-in user.

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "user": {
      "id": 12,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "avatar_url": "https://cdn.viewlix.online/avatars/avatar_12.png",
      "firebase_uuid": "c76a9be8-d218-4a69-a1b7-a859e9a4f48b",
      "is_admin": false
    },
    "stats": {
      "favorites_count": 24,
      "watch_history_count": 58,
      "tickets_count": 1
    }
  }
}
```

---

### `PUT /auth/profile` *(Auth Required)*
Update user name or email address.

**Request Body:**
```json
{
  "name": "Jane Smith",
  "email": "jane.smith@example.com"
}
```

---

### `PUT /auth/password` *(Auth Required)*
Change the user password.

**Request Body:**
```json
{
  "current_password": "OldPassword123!",
  "password": "NewSecretPassword456!",
  "password_confirmation": "NewSecretPassword456!"
}
```

---

### `POST /auth/avatar` *(Auth Required)*
Upload a custom user avatar.

- **Content-Type**: `multipart/form-data`
- **Field**: `avatar` (File: image/jpeg, image/png, image/webp; max 5MB)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Avatar updated successfully",
  "data": {
    "avatar_url": "https://cdn.viewlix.online/avatars/avatar_12_1746800000.png"
  }
}
```

---

### `GET /auth/firebase-token` *(Auth Required)*
Obtains a Firebase Custom Token for the logged-in user. Pass this token to `signInWithCustomToken(token)` in the Firebase Client SDK to authenticate real-time data listeners on Firebase Realtime Database.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "firebase_token": "eyJhbGciOiJSUzI1NiIsInR5cCI...",
    "firebase_uuid": "c76a9be8-d218-4a69-a1b7-a859e9a4f48b"
  }
}
```

---

### `POST /auth/logout` *(Auth Required)*
Revokes the current device token.

---

### `DELETE /auth/delete-account` *(Auth Required)*
Permanently deletes the account and all associated watch history and favorites.

**Request Body:**
```json
{
  "password": "CurrentPassword123!"
}
```

---

## 2. App Configuration & Updates

### Realtime App Configuration via Firebase RTDB
The mobile app can listen to real-time configurations via Firebase Realtime Database at `/app_config` (or `/mobile_app_config`). This allows instant, remote updates to player URLs, maintenance mode, and force updates without requiring an app store release.

**RTDB Path:** `/app_config`

**JSON Payload in RTDB:**
```json
{
  "app_name": "Viewlix",
  "version": "1.0.0",
  "download_url": "https://viewlix.online/download/android",
  "latest_apk": "https://viewlix.online/download/android",
  "player_url": "https://content1.viewlix.online/",
  "maintenance_mode": false,
  "force_update": false,
  "updated_at": 1793392533
}
```

---

### `GET /app/config`
Call this during mobile app launch to bootstrap configurations over REST API.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "app": {
      "name": "Viewlix",
      "url": "https://viewlix.online",
      "player_url": "https://content1.viewlix.online/",
      "maintenance_mode": false,
      "force_update": false
    },
    "player_url": "https://content1.viewlix.online/",
    "images": {
      "tmdb_base_url": "https://image.tmdb.org/t/p",
      "poster_sizes": ["w92", "w154", "w185", "w342", "w500", "w780", "original"],
      "backdrop_sizes": ["w300", "w780", "w1280", "original"],
      "proxy_enabled": true
    },
    "firebase": {
      "config": {
        "apiKey": "AIzaSy...",
        "authDomain": "viewlix-v2.firebaseapp.com",
        "databaseURL": "https://viewlix-v2-default-rtdb.firebaseio.com",
        "projectId": "viewlix-v2",
        "storageBucket": "viewlix-v2.appspot.com",
        "messagingSenderId": "...",
        "appId": "..."
      }
    },
    "mobile_versions": {
      "android": {
        "version": "2.1.0",
        "play_store_url": "https://play.google.com/store/apps/details?id=online.viewlix.app",
        "file_size": "28MB"
      },
      "ios": {
        "version": "2.1.0",
        "app_store_url": "https://apps.apple.com/app/viewlix/id123456789"
      }
    },
    "banners": [ ... ]
  }
}
```

---

### `GET /app/check-update`
Check whether the installed app version has an update available.

**Query Parameters:**
- `platform`: `android` or `ios` (default: `android`)
- `current_version`: e.g. `2.0.0`

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "update_available": true,
    "force_update": false,
    "current_version": "2.0.0",
    "latest_version": "2.1.0",
    "platform": "android",
    "download_url": "https://play.google.com/store/apps/details?id=online.viewlix.app",
    "file_size": "28MB"
  }
}
```

---

### `GET /banners`
Fetch active promotional carousels and announcement banners.

---

## 3. Home Feed

### `GET /home`
Aggregates all sections required for the mobile app home screen in a single fast call. If the request includes an `Authorization: Bearer <TOKEN>` header, the `continue_watching` array is populated.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "banners": [ ... ],
    "continue_watching": [
      {
        "id": 27205,
        "media_id": 27205,
        "media_type": "movie",
        "title": "Inception",
        "poster_url": "https://wsrv.nl/?url=...&w=500&output=webp&q=80",
        "backdrop_url": "https://wsrv.nl/?url=...&w=1280&output=webp&q=80",
        "progress": 64.5,
        "watched_time": 5740,
        "duration": 8880,
        "formatted_time": "1:35:40",
        "last_watched": "2026-10-30T11:20:00Z"
      }
    ],
    "trending_movies": [ ... ],
    "popular_movies": [ ... ],
    "top_rated_movies": [ ... ],
    "trending_tv": [ ... ],
    "popular_tv": [ ... ],
    "popular_anime": [ ... ],
    "filipino_movies": [ ... ]
  }
}
```

---

## 4. Movies

### `GET /movies`
List movies with filtering and pagination.

**Query Parameters:**
- `filter`: `popular` (default), `top_rated`, `now_playing`, `upcoming`, `trending`, `filipino`
- `page`: Page number (integer, default `1`)

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "filter": "popular",
    "page": 1,
    "total_pages": 500,
    "total_results": 10000,
    "results": [
      {
        "id": 933260,
        "title": "The Substance",
        "media_type": "movie",
        "overview": "A fading celebrity decides to use a black market drug...",
        "poster_path": "/lqoMzCcZYEFK729Fc65q8q9bph8.jpg",
        "backdrop_path": "/7h6TqPB3ESmPFVosdbmHG6Kh5Sl.jpg",
        "poster_url": "https://wsrv.nl/?url=https%3A%2F%2Fimage.tmdb.org%2Ft%2Fp%2Foriginal%2FlqoMzCcZYEFK729Fc65q8q9bph8.jpg&w=500&output=webp&q=80",
        "backdrop_url": "https://wsrv.nl/?url=https%3A%2F%2Fimage.tmdb.org%2Ft%2Fp%2Foriginal%2F7h6TqPB3ESmPFVosdbmHG6Kh5Sl.jpg&w=1280&output=webp&q=80",
        "release_date": "2024-09-07",
        "vote_average": 7.3
      }
    ]
  }
}
```

---

### `GET /movies/{id}`
Retrieve full movie details, genres, credits, YouTube trailers, similar titles, and user state (if authenticated).

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 27205,
    "title": "Inception",
    "overview": "Cobb steals information from targets by entering their dreams...",
    "poster_url": "https://wsrv.nl/?url=...&w=500&output=webp&q=80",
    "backdrop_url": "https://wsrv.nl/?url=...&w=1280&output=webp&q=80",
    "logo_url": "https://wsrv.nl/?url=...&w=500&output=webp&q=80",
    "runtime": 148,
    "genres": [
      { "id": 28, "name": "Action" },
      { "id": 878, "name": "Science Fiction" }
    ],
    "credits": {
      "cast": [ ... ],
      "crew": [ ... ]
    },
    "trailers": [
      {
        "id": "533ec654c3a36854480003eb",
        "name": "Official Trailer",
        "key": "YoHD9XEInc0",
        "youtube_url": "https://www.youtube.com/watch?v=YoHD9XEInc0",
        "type": "Trailer",
        "official": true
      }
    ],
    "similar": {
      "results": [ ... ]
    },
    "user_meta": {
      "is_favorited": true,
      "progress": {
        "watched_time": 5740,
        "duration": 8880,
        "progress": 64.5,
        "is_completed": false,
        "last_updated": "2026-10-30T11:20:00Z"
      }
    }
  }
}
```

---

### `GET /movies/{id}/sources`
Get available streaming/embed server URLs for a movie.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 27205,
    "type": "movie",
    "default_embed": "https://vidsrc.xyz/embed/movie/27205",
    "sources": [
      {
        "name": "VidSrc",
        "embed": "https://vidsrc.xyz/embed/movie/27205",
        "is_default": true,
        "source_id": 1
      },
      {
        "name": "SuperEmbed",
        "embed": "https://multiembed.mov/?video_id=27205&tmdb=1",
        "is_default": false,
        "source_id": 2
      }
    ]
  }
}
```

---

## 5. TV Shows & Seasons

### `GET /tv-shows`
List TV series with filtering and pagination.

**Query Parameters:**
- `filter`: `popular` (default), `top_rated`, `on_the_air`, `airing_today`, `trending`, `filipino`
- `page`: Page number (integer, default `1`)

---

### `GET /tv-shows/{id}`
Retrieve TV series details including list of all seasons, cast, trailers, and last watched episode (if authenticated).

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1399,
    "name": "Game of Thrones",
    "number_of_seasons": 8,
    "number_of_episodes": 73,
    "seasons": [
      {
        "id": 3624,
        "name": "Season 1",
        "season_number": 1,
        "episode_count": 10,
        "poster_url": "https://wsrv.nl/?url=...&w=342&output=webp&q=80"
      }
    ],
    "user_meta": {
      "is_favorited": true,
      "last_watched": {
        "season": 1,
        "episode": 4,
        "watched_time": 2400,
        "duration": 3600,
        "progress": 66.67
      }
    }
  }
}
```

---

### `GET /tv-shows/{id}/seasons/{season}`
Get detailed episode list for a specific season.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 3624,
    "season_number": 1,
    "name": "Season 1",
    "episodes": [
      {
        "id": 63056,
        "episode_number": 1,
        "name": "Winter Is Coming",
        "overview": "Jon Arryn, the Hand of the King, is dead...",
        "air_date": "2011-04-17",
        "runtime": 62,
        "vote_average": 7.9,
        "still_url": "https://wsrv.nl/?url=https%3A%2F%2Fimage.tmdb.org%2Ft%2Fp%2Foriginal%2FwrGWeW4WKxnaeA8sxJb2T9O2RQF.jpg&w=300&output=webp&q=80"
      }
    ]
  }
}
```

---

### `GET /tv-shows/{id}/seasons/{season}/episodes/{episode}/sources`
Get available streaming/embed server URLs for a TV episode.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1399,
    "type": "tv",
    "season": 1,
    "episode": 1,
    "default_embed": "https://vidsrc.xyz/embed/tv/1399/1/1",
    "sources": [
      {
        "name": "VidSrc",
        "embed": "https://vidsrc.xyz/embed/tv/1399/1/1",
        "is_default": true,
        "source_id": 1
      }
    ]
  }
}
```

---

## 6. Anime

### `GET /anime`
List anime series.
- `filter`: `popular` (default), `top_rated`, `airing`
- `page`: Page number (integer, default `1`)

### `GET /anime/{id}`
Anime details, seasons, and similar anime.

### `GET /anime/{id}/seasons/{season}`
Anime episodes list for a specific season.

### `GET /anime/{id}/seasons/{season}/episodes/{episode}/sources`
Streaming and embed URLs for an anime episode.

---

## 7. Search, Discovery & Providers

### `GET /search`
Search across movies, TV shows, and anime.

**Query Parameters:**
- `query` (or `q`): Search keyword (required)
- `type`: `all` (default), `movie`, `tv`, `anime`
- `page`: Page number (default `1`)

---

### `GET /search/suggestions`
Fast autocomplete suggestions intended for search bar inputs.

**Query Parameters:**
- `q`: Search keyword

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 27205,
      "title": "Inception",
      "media_type": "movie",
      "year": "2010",
      "vote_average": 8.37,
      "poster_url": "https://wsrv.nl/?url=...&w=185&output=webp&q=80"
    }
  ]
}
```

---

### `GET /genres`
List genres for movies and TV shows.

---

### `GET /discover`
Discover media by specific criteria.

**Query Parameters:**
- `type`: `movie` or `tv` (default: `movie`)
- `with_genres`: Comma-separated genre IDs (e.g. `28,12`)
- `sort_by`: e.g. `popularity.desc`, `vote_average.desc`
- `primary_release_year`: Year (e.g. `2024`)
- `page`: Page number (default: `1`)

---

### `GET /providers`
Content by popular streaming provider.

**Query Parameters:**
- `provider`: `netflix`, `disney`, `hbo`, `prime`, `apple`, `hulu`, `peacock`
- `type`: `movie` or `tv` (default: `movie`)
- `page`: Page number (default: `1`)

---

## 8. Favorites & Watchlist

> **All endpoints in this section require an `Authorization: Bearer <TOKEN>` header.**

### `GET /favorites`
List user favorites.
- `type`: `all` (default), `movie`, `tv`, `anime`
- `page`: Page number (default `1`)
- `per_page`: Number of items (default `24`, max `50`)

---

### `POST /favorites/toggle`
One-tap add or remove from watchlist.

**Request Body:**
```json
{
  "media_id": 27205,
  "media_type": "movie",
  "title": "Inception",
  "poster_path": "/xlaY2zyzMfkhk0HSC5VUwzoZPU1.jpg",
  "backdrop_path": "/8ZTVqvKDQ8emSGUEMjsS4yHAwrp.jpg",
  "vote_average": 8.37,
  "release_date": "2010-07-15"
}
```

**Response (200 OK or 201 Created):**
```json
{
  "success": true,
  "message": "Added to favorites",
  "data": {
    "status": "added",
    "is_favorited": true,
    "favorite_id": 42
  }
}
```

---

### `GET /favorites/check/{mediaId}`
Check if a specific title is already favorited.
- Query parameter: `?type=movie|tv|anime`

---

### `DELETE /favorites/{mediaId}`
Remove an item from favorites.
- Query parameter: `?type=movie|tv|anime`

---

## 9. Watch History & Realtime Sync

> **All endpoints in this section require an `Authorization: Bearer <TOKEN>` header.**

### `GET /watch-history`
List continue-watching titles.
- `type`: `movie`, `tv`, `anime` (optional filter)
- `page`: Page number
- `per_page`: Number of items (default `20`, max `50`)

---

### `POST /watch-history`
Record playback progress. This automatically updates the database and syncs directly to Firebase Realtime Database for instant multi-device continuity.

**Request Body:**
```json
{
  "media_id": 1399,
  "media_type": "tv",
  "title": "Game of Thrones",
  "poster_path": "/u3bZgnGQ9T01sWNhyveQz0wH0Hl.jpg",
  "backdrop_path": "/2OMB0ynKlyIenMJWI2Dy9IWT4c.jpg",
  "season": 1,
  "episode": 4,
  "watched_time": 2150,
  "duration": 3480
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Progress updated successfully",
  "data": {
    "id": 91,
    "media_id": 1399,
    "progress": 61.78,
    "is_completed": false,
    "updated_at": "2026-10-30T12:05:00Z"
  }
}
```

---

### `GET /watch-history/{mediaId}`
Fetch saved playback timestamp and progress for a media item.
- Query parameter: `?type=movie|tv|anime`

---

### `DELETE /watch-history/{mediaId}`
Remove a title from continue watching.

---

### `DELETE /watch-history`
Clear complete watch history.

---

## 10. In-App Support & Chat

> **All endpoints in this section require an `Authorization: Bearer <TOKEN>` header.**

### `GET /support/tickets`
List user support tickets.

---

### `POST /support/tickets`
Create a new support ticket.

**Request Body:**
```json
{
  "subject": "Playback issue on Episode 3",
  "message": "The stream stopped loading at 14:20 with an error.",
  "category": "streaming",
  "priority": "normal"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Support ticket submitted successfully",
  "data": {
    "ticket_id": "VLX-TKT-9B41E2",
    "session_id": "sess_tkt_VLX_TKT_9B41E2",
    "ticket": { ... }
  }
}
```

---

### `GET /support/tickets/{ticketId}`
Retrieve ticket details and full conversation message history.

---

### `POST /support/tickets/{ticketId}/messages`
Send a reply message in an open ticket thread.

**Request Body:**
```json
{
  "text": "I tried restarting the app and the issue persists."
}
```

---

### `POST /support/tickets/{ticketId}/reopen`
Request reopening a resolved or closed ticket.

**Request Body:**
```json
{
  "reason": "The issue happened again on the same video."
}
```

---

## 11. Mobile Implementation Examples

### Flutter / Dart (`http` or `dio`)

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class ViewlixApi {
  static const String baseUrl = 'https://viewlix.online/api/v1';
  String? authToken;

  ViewlixApi({this.authToken});

  Map<String, String> get headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (authToken != null) 'Authorization': 'Bearer $authToken',
  };

  // 1. App Configuration
  Future<Map<String, dynamic>> getAppConfig() async {
    final response = await http.get(Uri.parse('$baseUrl/app/config'), headers: headers);
    return jsonDecode(response.body)['data'];
  }

  // 1b. Google Sign-In (Unified Login & Register for Android)
  Future<Map<String, dynamic>> signInWithGoogle(String idToken, {String deviceName = 'Android App'}) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/google'),
      headers: headers,
      body: jsonEncode({
        'id_token': idToken,
        'device_name': deviceName,
      }),
    );
    final json = jsonDecode(response.body);
    if (json['success'] == true) {
      authToken = json['data']['token'];
    }
    return json;
  }

  // 2. Home Feed
  Future<Map<String, dynamic>> getHomeFeed() async {
    final response = await http.get(Uri.parse('$baseUrl/home'), headers: headers);
    return jsonDecode(response.body)['data'];
  }

  // 3. Movie Streaming Sources
  Future<List<dynamic>> getMovieSources(int movieId) async {
    final response = await http.get(Uri.parse('$baseUrl/movies/$movieId/sources'), headers: headers);
    return jsonDecode(response.body)['data']['sources'];
  }

  // 4. Sync Watch Progress
  Future<void> saveProgress({
    required int mediaId,
    required String mediaType,
    required String title,
    required int watchedTime,
    required int duration,
    int season = 1,
    int episode = 1,
  }) async {
    await http.post(
      Uri.parse('$baseUrl/watch-history'),
      headers: headers,
      body: jsonEncode({
        'media_id': mediaId,
        'media_type': mediaType,
        'title': title,
        'watched_time': watchedTime,
        'duration': duration,
        'season': season,
        'episode': episode,
      }),
    );
  }
}
```

---

### React Native / Axios

```typescript
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://viewlix.online/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

export const setAuthToken = (token?: string) => {
  if (token) {
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  } else {
    delete api.defaults.headers.common['Authorization'];
  }
};

// 1. User Login
export const login = async (email: string, password: string) => {
  const response = await api.post('/auth/login', { email, password, device_name: 'Mobile App' });
  const { token, user } = response.data.data;
  setAuthToken(token);
  return { token, user };
};

// 2. Fetch TV Episode Sources
export const getEpisodeSources = async (tvId: number, season: number, episode: number) => {
  const response = await api.get(`/tv-shows/${tvId}/seasons/${season}/episodes/${episode}/sources`);
  return response.data.data;
};

// 3. Toggle Favorite
export const toggleFavorite = async (item: { media_id: number; media_type: string; title: string }) => {
  const response = await api.post('/favorites/toggle', item);
  return response.data.data;
};
```

