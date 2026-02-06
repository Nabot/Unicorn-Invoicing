# SSH Setup for cPanel Server

## Step 1: Save Your SSH Private Key

### On Mac/Linux:

1. **Create .ssh directory** (if it doesn't exist):
   ```bash
   mkdir -p ~/.ssh
   chmod 700 ~/.ssh
   ```

2. **Save the private key**:
   ```bash
   nano ~/.ssh/cpanel_unicorn_key
   ```
   
   Paste your private key (the entire content including `-----BEGIN OPENSSH PRIVATE KEY-----` and `-----END OPENSSH PRIVATE KEY-----`)
   
   Save and exit (Ctrl+X, then Y, then Enter)

3. **Set proper permissions** (IMPORTANT for security):
   ```bash
   chmod 600 ~/.ssh/cpanel_unicorn_key
   ```

### On Windows (using PowerShell):

1. **Create .ssh directory**:
   ```powershell
   mkdir $HOME\.ssh
   ```

2. **Save the key**:
   ```powershell
   notepad $HOME\.ssh\cpanel_unicorn_key
   ```
   
   Paste your private key and save

3. **Set permissions** (in PowerShell as Administrator):
   ```powershell
   icacls $HOME\.ssh\cpanel_unicorn_key /inheritance:r
   icacls $HOME\.ssh\cpanel_unicorn_key /grant:r "%USERNAME%:R"
   ```

## Step 2: Find Your Server Details

You'll need:
- **Server IP or Hostname**: Usually provided by your hosting provider
- **SSH Port**: Usually `22` (default) or `2222` (some cPanel hosts)
- **Username**: Your cPanel username

## Step 3: Connect via SSH

### On Mac/Linux:

```bash
ssh -i ~/.ssh/cpanel_unicorn_key username@your-server-ip-or-hostname -p 22
```

Replace:
- `username` with your cPanel username
- `your-server-ip-or-hostname` with your server address
- `22` with your SSH port (if different)

### On Windows (using PowerShell or Git Bash):

```bash
ssh -i $HOME\.ssh\cpanel_unicorn_key username@your-server-ip-or-hostname -p 22
```

### If you get "Permission denied (publickey)":

Try adding verbose mode to see what's happening:
```bash
ssh -v -i ~/.ssh/cpanel_unicorn_key username@your-server-ip-or-hostname -p 22
```

## Step 4: Configure SSH Config (Optional but Recommended)

Create/edit `~/.ssh/config`:

```bash
nano ~/.ssh/config
```

Add:
```
Host cpanel-unicorn
    HostName your-server-ip-or-hostname
    User your-cpanel-username
    IdentityFile ~/.ssh/cpanel_unicorn_key
    Port 22
```

Then you can connect simply with:
```bash
ssh cpanel-unicorn
```

## Step 5: Once Connected

After successfully connecting, you'll be in your home directory. Navigate to your Laravel application:

```bash
# List files to find your Laravel folder
ls -la

# Navigate to your Laravel application
cd ~/Unicorn-Invoicing
# or
cd ~/laravel_app
# or wherever you cloned the repository
```

## Troubleshooting

### Issue: "Permission denied (publickey)"

**Solutions:**
1. Check key permissions: `chmod 600 ~/.ssh/cpanel_unicorn_key`
2. Verify key format (should start with `-----BEGIN OPENSSH PRIVATE KEY-----`)
3. Check if your hosting provider requires password authentication instead
4. Contact your hosting provider to enable SSH key authentication

### Issue: "Connection refused"

**Solutions:**
1. Check if SSH is enabled in cPanel
2. Verify the port (try 22, 2222, or check with hosting provider)
3. Check firewall settings
4. Verify server IP/hostname is correct

### Issue: "Host key verification failed"

**Solution:**
```bash
ssh-keygen -R your-server-ip-or-hostname
```

Then try connecting again.

## Security Best Practices

1. **Never share your private key** - Keep it secure
2. **Use 600 permissions** - Only you should be able to read the key
3. **Consider using SSH agent** - To avoid entering passphrase repeatedly:
   ```bash
   ssh-add ~/.ssh/cpanel_unicorn_key
   ```
4. **Use strong passphrases** - If your key has a passphrase, make it strong
5. **Disable password authentication** - Once key-based auth works, disable password auth in cPanel

## Next Steps After SSH Connection

Once you're connected via SSH, follow the setup steps in `CPANEL_SETUP_STEPS.md`:

1. Configure `.env` file
2. Generate application key
3. Install dependencies
4. Set permissions
5. Run migrations
6. Seed database
7. Cache configuration
