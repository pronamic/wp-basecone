<p align="center">
	<a href="https://github.com/pronamic/wp-basecone">
		<img src="logos/basecone-icon.svg" alt="Pronamic WordPress Basecone" width="128" height="128">
	</a>
</p>

<h1 align="center">Pronamic WordPress Basecone</h3>

<p align="center">
	The Pronamic WordPress Basecone plugin allows you to connect your WordPress installation to Basecone.
</p>

## Table of contents

- [Authentication](#authentication)
- [REST API](#rest-api)
- [WP-CLI](#wp-cli)
- [Environment variables](#environment-variables)
- [Links](#links)

## Authentication

Once you have received a `clientIdentifier` and `clientSecret` from Basecone, you can request an API user access key via the [`Authentication/ApiAccessKeys` endpoint](https://developers.basecone.com/ApiReference/ApiUserAccessKeys).

## REST API

> The WordPress REST API provides an interface for applications to interact with your WordPress site by sending and receiving data as JSON (JavaScript Object Notation) objects.

## WP-CLI

> WP-CLI is the command-line interface for WordPress. You can update plugins, configure multisite installations and much more, without using a web browser.

### Document import

https://developers.basecone.com/ApiReference/DocumentImport

```
wp basecone import test.pdf --company_id=846b6ff6-8659-4ff9-813a-ce1b16c5d1bf
```

## Environment variables

- `BASECONE_CLIENT_IDENTIFIER`
- `BASECONE_CLIENT_SECRET`
- `BASECONE_API_ACCESS_KEY`

## Links

- https://developers.basecone.com/
- https://developers.basecone.com/ApiReference/General
- https://www.pronamic.nl/
- https://www.remcotolsma.nl/
