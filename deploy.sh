#!/bin/bash

echo "🚀 Starting deployment process..."

# Build the Docker image
echo "📦 Building Docker image..."
docker build -t allsports:latest .

# Stop and remove existing container if running
echo "🛑 Stopping existing container..."
docker stop allsports 2>/dev/null || true
docker rm allsports 2>/dev/null || true

# Run the new container
echo "🏃 Starting new container..."
docker run -d \
  --name allsports \
  -p 8080:80 \
  --restart unless-stopped \
  allsports:latest

echo "✅ Deployment completed!"
echo "🌐 Application is running at http://localhost:8080"

# Show logs
echo "📋 Container logs:"
docker logs allsports --tail 20