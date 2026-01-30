# ZIVPN UDP Server - Dual API Installation

## 📋 Overview

ZIVPN is a UDP-based VPN server with dual API servers designed for easy management through both APK applications and web-based dashboards. This installer provides a complete setup with domain support, SSL encryption, multi-password authentication, and automated user management.

## ✨ Features

- **Dual API Servers**: Separate API endpoints for APK Manager (port 8080) and Website Panel (port 8090)
- **Domain Support**: Use custom domain or server IP
- **SSL Encryption**: Auto-generated SSL certificates
- **Multi-password Authentication**: Support for multiple user passwords
- **Auto-expiration**: Automated user expiry system with cron jobs
- **User Management**: Create, delete, renew, and list users via API
- **CORS Enabled**: Proper CORS headers for web dashboard integration
- **Fallback Systems**: Python fallback if Go compilation fails
- **Systemd Integration**: Managed as system services
- **Firewall Configuration**: Automatic port opening

## 🏗️ System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   APK Manager   │    │  Web Dashboard  │    │  VPN Clients    │
│   (Android)     │    │   (HTML/JS)     │    │   (UDP)         │
└────────┬────────┘    └────────┬────────┘    └────────┬────────┘
         │                      │                      │
         ▼                      ▼                      ▼
┌─────────────────────────────────────────────────────────────┐
│                    ZIVPN SERVER                            │
│                                                            │
│  ┌─────────────┐  ┌─────────────┐  ┌──────────────────┐  │
│  │ API Server  │  │ Web API     │  │ VPN UDP Server   │  │
│  │ Port: 8080  │  │ Port: 8090  │  │ Port: 5667       │  │
│  └─────────────┘  └─────────────┘  └──────────────────┘  │
│                                                            │
│  ┌────────────────────────────────────────────────────┐  │
│  │                User Database                       │  │
│  │                (/etc/zivpn/)                       │  │
│  └────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## 📦 Requirements

- **Operating System**: Ubuntu 18.04+, Debian 9+, or other Linux distributions
- **Architecture**: x86_64 (amd64) or ARM64
- **Memory**: Minimum 512MB RAM
- **Storage**: 1GB free disk space
- **Network**: Public IP or domain with port forwarding
- **Privileges**: Root access

## 🚀 Installation

### Quick Install

```bash
# Download and run the installer
sudo wget -O zivpn-installer.sh https://raw.githubusercontent.com/your-repo/zivpn/main/installer.sh
sudo chmod +x zivpn-installer.sh
sudo ./zivpn-installer.sh
```

### Interactive Installation Steps

1. **Domain Setup**: Enter your domain or use server IP
2. **API Key Setup**: Generate or enter custom API key
3. **Port Configuration**: Set website API port (default: 8090)
4. **Password Setup**: Add comma-separated user passwords
5. **Automatic Setup**: Script handles compilation and service setup

## ⚙️ Configuration Files

| File | Location | Purpose |
|------|----------|---------|
| Main Config | `/etc/zivpn/config.json` | VPN server configuration |
| User Database | `/etc/zivpn/users.json` | User credentials and expiry |
| Domain File | `/etc/zivpn/domain` | Server domain/IP |
| API Key | `/etc/zivpn/apikey` | Authentication token |
| SSL Certificates | `/etc/zivpn/ssl/` | SSL certificates |
| API Server (APK) | `/etc/zivpn/api/` | APK Manager API |
| API Server (Web) | `/etc/zivpn/api-web/` | Website Panel API |

## 🌐 API Documentation

### Base URLs
- **APK API**: `http://your-domain:8080`
- **Web API**: `http://your-domain:8090`

### Authentication
Include API key in headers:
```http
X-API-Key: your-api-key-here
```

Or as query parameter:
```
?api_key=your-api-key-here
```

### Health Check (No Auth Required)

```http
GET /api/health
```

**Response:**
```json
{
  "status": "online",
  "service": "zivpn-api",
  "version": "1.0",
  "port": "8090",
  "time": "2024-01-15 10:30:45"
}
```

### User Management Endpoints

#### Create User
```http
POST /api/user/create
Content-Type: application/json

{
  "password": "user123",
  "days": 30
}
```

**Response:**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "password": "user123",
    "expired": "2024-02-14",
    "domain": "vpn.example.com",
    "port": "5667",
    "api_port": "8090"
  }
}
```

#### Delete User
```http
POST /api/user/delete
Content-Type: application/json

{
  "password": "user123"
}
```

#### Renew User
```http
POST /api/user/renew
Content-Type: application/json

{
  "password": "user123",
  "days": 15
}
```

#### List Users
```http
GET /api/users
```

**Response:**
```json
{
  "success": true,
  "message": "User list",
  "data": [
    {
      "password": "user123",
      "expired": "2024-02-14",
      "status": "Active"
    }
  ]
}
```

### System Information

```http
GET /api/info
```

**Response:**
```json
{
  "success": true,
  "message": "System Info",
  "data": {
    "domain": "vpn.example.com",
    "public_ip": "203.0.113.1",
    "private_ip": "192.168.1.100",
    "port": "5667",
    "api_port": "8090",
    "service": "zivpn",
    "api_type": "website",
    "api_status": "online",
    "api_version": "1.0"
  }
}
```

### Website-Specific Endpoints

#### Get Website Configuration
```http
GET /api/website/config
```

#### Test Website Connection
```http
GET /api/website/test
```

#### Get Website Status
```http
GET /api/website/status
```

## 🖥️ Web Dashboard Integration

### HTML Dashboard Setup

1. **Download HTML Template**:
```bash
wget -O dashboard.html https://raw.githubusercontent.com/your-repo/zivpn/main/dashboard.html
```

2. **Configure Dashboard**:
```html
<script>
const config = {
  server: "your-domain.com",
  port: "8090",
  apiKey: "your-api-key-here"
};
</script>
```

3. **Features**:
   - User management (create/delete/renew)
   - Real-time status monitoring
   - User list with expiry tracking
   - Server statistics

### API Integration Example

```javascript
async function getUsers() {
  const response = await fetch('http://your-domain:8090/api/users', {
    headers: {
      'X-API-Key': 'your-api-key-here',
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
}

async function createUser(password, days) {
  const response = await fetch('http://your-domain:8090/api/user/create', {
    method: 'POST',
    headers: {
      'X-API-Key': 'your-api-key-here',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ password, days })
  });
  return await response.json();
}
```

## 🔧 Management Commands

### Service Control

```bash
# Start services
sudo systemctl start zivpn
sudo systemctl start zivpn-api       # APK API (8080)
sudo systemctl start zivpn-api-web   # Web API (8090)

# Stop services
sudo systemctl stop zivpn
sudo systemctl stop zivpn-api
sudo systemctl stop zivpn-api-web

# Check status
sudo systemctl status zivpn
sudo systemctl status zivpn-api
sudo systemctl status zivpn-api-web

# Enable auto-start
sudo systemctl enable zivpn
sudo systemctl enable zivpn-api
sudo systemctl enable zivpn-api-web
```

### Logs Monitoring

```bash
# View ZIVPN logs
sudo journalctl -u zivpn -f

# View API logs
sudo journalctl -u zivpn-api -f
sudo journalctl -u zivpn-api-web -f

# Installation log
cat /tmp/zivpn_install_*.log
```

### Utility Commands

```bash
# Test API connections
sudo ./zivpn-installer.sh --test

# Uninstall ZIVPN
sudo ./zivpn-installer.sh --uninstall

# Manual cron execution
curl -X POST -H "X-API-Key: YOUR_KEY" http://localhost:8090/api/cron/expire
```

## 🔐 Security Considerations

1. **API Key Protection**:
   - Keep API key confidential
   - Rotate keys periodically
   - Use HTTPS in production

2. **Firewall Rules**:
   - Only open necessary ports
   - Consider IP whitelisting for API access
   - Use fail2ban for brute-force protection

3. **SSL Certificates**:
   - Replace self-signed certificates with Let's Encrypt
   - Regular certificate renewal

4. **User Management**:
   - Implement strong password policies
   - Regular audit of user database
   - Monitor for suspicious activity

## 🐛 Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| Port 8080/8090 not accessible | Check firewall: `sudo ufw status` |
| API returns "Unauthorized" | Verify API key in `/etc/zivpn/apikey` |
| VPN connection fails | Check UDP port 5667 is open |
| Go compilation fails | Install Go: `sudo apt install golang` |
| Python API not starting | Install Python3: `sudo apt install python3` |
| Service not starting | Check logs: `sudo journalctl -u service-name` |

### Diagnostic Commands

```bash
# Check open ports
sudo ss -tulpn | grep -E '(8080|8090|5667)'

# Test API connectivity
curl -s http://localhost:8080/api/health
curl -s http://localhost:8090/api/health

# Verify user database
cat /etc/zivpn/users.json | jq .

# Check systemd services
systemctl list-units --type=service | grep zivpn
```

## 📊 Monitoring

### Health Monitoring Endpoints

```bash
# Basic health check
curl http://your-domain:8090/api/health

# Detailed system info
curl -H "X-API-Key: YOUR_KEY" http://your-domain:8090/api/info

# User statistics
curl -H "X-API-Key: YOUR_KEY" http://your-domain:8090/api/website/status
```

### Performance Metrics

- **UDP Connections**: Monitor on port 5667
- **API Response Time**: Should be < 500ms
- **Memory Usage**: ZIVPN typically uses 50-100MB RAM
- **CPU Usage**: Minimal under normal load

## 🔄 Updates & Maintenance

### Updating ZIVPN

```bash
# Backup current configuration
sudo cp -r /etc/zivpn /etc/zivpn_backup_$(date +%Y%m%d)

# Run installer again (it will update)
sudo ./zivpn-installer.sh
```

### Regular Maintenance Tasks

1. **Daily**: Check expiration cron job
2. **Weekly**: Review logs for errors
3. **Monthly**: Rotate API keys
4. **Quarterly**: Update SSL certificates
5. **As needed**: Review and clean user database

## 📝 License

This project is licensed under the MIT License. See the LICENSE file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📞 Support

- **GitHub Issues**: For bug reports and feature requests
- **Documentation**: Check this README first
- **Community**: Join our Discord/Telegram group

## 🚨 Disclaimer

This software is provided "as-is" without any warranties. Users are responsible for:
- Complying with local laws and regulations
- Securing their own servers
- Proper firewall configuration
- Regular security updates

---

**Last Updated**: January 2024  
**Version**: 1.4.9  
**Author**: ZIVPN Team  
**Website**: https://github.com/zahidbd2/udp-zivpn
