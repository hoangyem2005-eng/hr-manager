#!/bin/bash

# MobiFone WorkHub - Setup Script
# This script sets up the development environment for WorkHub

echo "🚀 MobiFone WorkHub - Development Setup"
echo "======================================"

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install Node.js from https://nodejs.org/"
    exit 1
fi

echo "✅ npm is installed: $(npm --version)"

# Install dependencies
echo ""
echo "📦 Installing dependencies..."
npm install

# Check if installation was successful
if [ $? -eq 0 ]; then
    echo "✅ Dependencies installed successfully"
else
    echo "❌ Failed to install dependencies"
    exit 1
fi

# Display next steps
echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "📋 Next steps:"
echo "  1. Start development server:"
echo "     npm run dev"
echo ""
echo "  2. Build for production:"
echo "     npm run build"
echo ""
echo "  3. Access the application:"
echo "     http://localhost:8000/workhub"
echo ""
echo "📚 For more information, see WORKHUB_INTEGRATION.md"
