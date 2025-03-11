# Node Custom Create Module

This module allows external POST requests to create nodes of type `article` in Drupal. The request must be authenticated using a **Bearer token** passed in the `Authorization` header.

## Features

- **POST Endpoint**: `/node-custom-create/create-node`
- **Authentication**: Bearer token authorization

## Requirements

- **Drupal 9 or 10 or 11**
- **PHP 7.3+**

## Configure the Bearer Token
To authenticate requests, configure the Bearer token by adding it
to the settings form.

- Go to /admin/config/node-custom-create.
- Add a value for the Bearer Token field.
- Save the configuration.

## API Usage

### Request

**POST /node-custom-create/create-node**

#### Headers:
- `Authorization: Bearer YOUR_BEARER_TOKEN`

#### Request Body (JSON):
```json
{
  "title": "Example Node Title",
  "body": "This is the body content of the node.",
  "tag": "example-tag",
  "image_url": "http://example.com/image.jpg"
}
```

### Response

#### Success (HTTP 200)
```json
{
  "message": "Node created successfully",
  "node_id": 123
}
```

#### Unauthorized (HTTP 401)
```json
{
  "message": "Unauthorized"
}
```

#### Missing Fields (HTTP 400)
```json
{
  "message": "Missing required fields"
}
```

#### Example cURL Request
```bash
curl -X POST "http://your-drupal-site/node-custom-create/create-node" \
-H "Authorization: Bearer YOUR_BEARER_TOKEN" \
-H "Content-Type: application/json" \
-d '{
  "title": "Example Node Title",
  "body": "This is the body content of the node.",
  "tag": "example-tag",
  "image_url": "http://example.com/image.jpg"
}'
```

## Delete Event Subscriber

This module includes an **Event Subscriber** that listens for node deletion
events. Whenever a node is deleted, this subscriber performs actions like
logging the event or cleaning up custom data.

