#!/bin/bash

# SSH Connection Script for cPanel Server
# Usage: ./connect_ssh.sh

# Configuration - UPDATE THESE VALUES
SSH_KEY="$HOME/.ssh/cpanel_unicorn_key"
SSH_USER="your-cpanel-username"
SSH_HOST="your-server-ip-or-hostname"
SSH_PORT="22"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== cPanel SSH Connection Script ===${NC}\n"

# Check if key exists
if [ ! -f "$SSH_KEY" ]; then
    echo -e "${RED}Error: SSH key not found at $SSH_KEY${NC}"
    echo -e "${YELLOW}Please save your private key to: $SSH_KEY${NC}"
    echo -e "${YELLOW}And set permissions: chmod 600 $SSH_KEY${NC}"
    exit 1
fi

# Check key permissions
KEY_PERMS=$(stat -f "%OLp" "$SSH_KEY" 2>/dev/null || stat -c "%a" "$SSH_KEY" 2>/dev/null)
if [ "$KEY_PERMS" != "600" ]; then
    echo -e "${YELLOW}Warning: Key permissions are $KEY_PERMS, should be 600${NC}"
    echo -e "${YELLOW}Setting permissions...${NC}"
    chmod 600 "$SSH_KEY"
fi

# Check if configuration needs to be updated
if [ "$SSH_USER" == "your-cpanel-username" ] || [ "$SSH_HOST" == "your-server-ip-or-hostname" ]; then
    echo -e "${RED}Error: Please update the configuration in this script first!${NC}"
    echo -e "${YELLOW}Edit connect_ssh.sh and set:${NC}"
    echo -e "  SSH_USER=\"your-actual-cpanel-username\""
    echo -e "  SSH_HOST=\"your-actual-server-ip-or-hostname\""
    echo -e "  SSH_PORT=\"22\" (or your SSH port)"
    exit 1
fi

echo -e "${GREEN}Connecting to $SSH_USER@$SSH_HOST:$SSH_PORT...${NC}\n"

# Connect via SSH
ssh -i "$SSH_KEY" "$SSH_USER@$SSH_HOST" -p "$SSH_PORT"
