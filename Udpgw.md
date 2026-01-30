# ZIVPN Complete Installation Script

## 📋 Overview

ZIVPN Complete adalah skrip instalasi all-in-one yang menggabungkan ZIVPN UDP Server, UDP Gateway, dan Dual API Servers untuk manajemen user yang lengkap. Skrip ini menyediakan solusi VPN yang komprehensif dengan antarmuka manajemen berbasis web dan API.

## ✨ Features

### 🚀 Core Services
- **ZIVPN UDP Server** - VPN server utama pada port 5667
- **UDPGW (mukswilly/udpgw)** - UDP Gateway built from source pada port 7300
- **Dual API Servers** - Dua server API untuk berbagai kebutuhan

### 🔧 Dual API System
| API Server | Port | Purpose | Authentication |
|------------|------|---------|----------------|
| APK API | 8080 | Untuk APK Manager | API Key Header |
| Website API | 8090 | Untuk Website Panel | API Key Header + CORS |

### 🔐 Security Features
- SSL/TLS encryption dengan sertifikat self-signed
- Multiple password authentication
- API key protection
- CORS support untuk website API
- Auto-expiration user dengan cron job

### 🌐 Network Configuration
- UDP port range: 6000-19999
- Port forwarding otomatis
- Kernel optimization
- Firewall rules configuration

## 📋 System Requirements

### Minimum Requirements
- **OS**: Ubuntu 18.04+, Debian 10+
- **Architecture**: x86_64 (amd64) atau aarch64 (arm64)
- **RAM**: 512 MB minimum
- **Storage**: 1 GB available space
- **Root access**: Required

### Dependencies
- Go 1.18+ (untuk kompilasi UDPGW)
- Python 3 (fallback API)
- OpenSSL
- iptables/ufw

## 🚀 Installation

### Quick Install
```bash
# Download script
wget https://raw.githubusercontent.com/your-repo/zivpn-complete.sh

# Make executable
chmod +x zivpn-complete.sh

# Run installer
sudo ./zivpn-complete.sh
```

### Interactive Installation
Script akan menanyakan:
1. **Domain/IP address** - Domain Anda atau otomatis menggunakan IP server
2. **API Key** - Generate otomatis atau masukkan manual
3. **Website API Port** - Default 8090 (dapat disesuaikan)
4. **Passwords** - Multiple passwords dipisahkan koma

## 📁 File Structure

```
/etc/zivpn/
├── config.json              # ZIVPN configuration
├── domain                   # Domain/IP file
├── apikey                   # API key file
├── ssl/
│   ├── zivpn.crt           # SSL certificate
│   └── zivpn.key           # SSL private key
├── users.json              # User database
├── api/                    # APK API Server
│   ├── z-api.go
│   ├── go.mod
│   └── zivpn-api (binary)
└── api-web/               # Website API Server
    ├── z-web-api.go
    ├── go.mod
    └── zivpn-api-web (binary)

/etc/udpgw/
└── udpgw.json              # UDPGW configuration

/usr/local/bin/
├── zivpn                   # ZIVPN binary
└── udpgw                   # UDPGW binary
```

## 📡 API Documentation

### Base URLs
- **APK API**: `http://your-domain:8080`
- **Website API**: `http://your-domain:8090`

### Authentication
Gunakan header `X-API-Key` dengan API key yang disimpan di `/etc/zivpn/apikey`

```bash
curl -H "X-API-Key: YOUR_API_KEY" http://your-domain:8090/api/users
```

### Health Check Endpoints

#### APK API
```http
GET /api/health
```
**Response:**
```json
{
  "status": "online",
  "service": "zivpn-api",
  "version": "1.0"
}
```

#### Website API
```http
GET /api/health
```
**Response:**
```json
{
  "status": "online",
  "service": "zivpn-website-api",
  "version": "1.0",
  "port": "8090",
  "time": "2024-01-30 14:30:00"
}
```

### User Management API

#### 1. Create User
```http
POST /api/user/create
Content-Type: application/json
X-API-Key: YOUR_API_KEY
```
**Request Body:**
```json
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
    "expired": "2024-02-29",
    "domain": "vpn.example.com",
    "port": "5667",
    "api_port": "8090"
  }
}
```

#### 2. Delete User
```http
POST /api/user/delete
Content-Type: application/json
X-API-Key: YOUR_API_KEY
```
**Request Body:**
```json
{
  "password": "user123"
}
```
**Response:**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

#### 3. Renew User
```http
POST /api/user/renew
Content-Type: application/json
X-API-Key: YOUR_API_KEY
```
**Request Body:**
```json
{
  "password": "user123",
  "days": 30
}
```
**Response:**
```json
{
  "success": true,
  "message": "User renewed successfully",
  "data": {
    "password": "user123",
    "expired": "2024-03-30"
  }
}
```

#### 4. List Users
```http
GET /api/users
X-API-Key: YOUR_API_KEY
```
**Response:**
```json
{
  "success": true,
  "message": "User list",
  "data": [
    {
      "password": "user1",
      "expired": "2024-02-29",
      "status": "Active"
    },
    {
      "password": "user2",
      "expired": "2024-01-15",
      "status": "Expired"
    }
  ]
}
```

### Website-Specific API

#### 1. Get Configuration
```http
GET /api/website/config
X-API-Key: YOUR_API_KEY
```
**Response:**
```json
{
  "success": true,
  "message": "Website configuration",
  "data": {
    "domain": "vpn.example.com",
    "api_port": "8090",
    "vpn_port": "5667",
    "api_key": "YOUR_API_KEY_HERE"
  }
}
```

#### 2. Get System Status
```http
GET /api/website/status
X-API-Key: YOUR_API_KEY
```
**Response:**
```json
{
  "success": true,
  "message": "Website status",
  "data": {
    "total_users": 15,
    "active_users": 12,
    "expired_users": 3,
    "system_uptime": "up 2 days, 3 hours",
    "api_version": "1.0",
    "last_checked": "2024-01-30 14:30:00"
  }
}
```

#### 3. Test Connection
```http
GET /api/website/test
X-API-Key: YOUR_API_KEY
```
**Response:**
```json
{
  "success": true,
  "message": "Connection test",
  "data": {
    "vpn_service": "active",
    "api_service": "active",
    "api_port": "8090",
    "timestamp": "2024-01-30 14:30:00",
    "status": "online"
  }
}
```

#### 4. System Information
```http
GET /api/info
X-API-Key: YOUR_API_KEY
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

### Auto-Expiration Endpoint

#### Manual Expiration Check
```http
POST /api/cron/expire
X-API-Key: YOUR_API_KEY
```
**Response:**
```json
{
  "success": true,
  "message": "Expiration check complete. Revoked: 2"
}
```

## 🛠 Management Commands

### Service Management
```bash
# Start all services
sudo systemctl start zivpn udpgw zivpn-api zivpn-api-web

# Stop all services
sudo systemctl stop zivpn udpgw zivpn-api zivpn-api-web

# Restart all services
sudo systemctl restart zivpn udpgw zivpn-api zivpn-api-web

# Check status
sudo systemctl status zivpn udpgw zivpn-api zivpn-api-web

# Enable auto-start on boot
sudo systemctl enable zivpn udpgw zivpn-api zivpn-api-web
```

### Service Logs
```bash
# View ZIVPN logs
sudo journalctl -u zivpn -f

# View UDPGW logs
sudo journalctl -u udpgw -f

# View APK API logs
sudo journalctl -u zivpn-api -f

# View Website API logs
sudo journalctl -u zivpn-api-web -f

# View combined logs
sudo journalctl -u zivpn* -f
```

### Manual Testing
```bash
# Test APK API
curl http://localhost:8080/api/health

# Test Website API
curl http://localhost:8090/api/health

# Test with API key
API_KEY=$(cat /etc/zivpn/apikey)
curl -H "X-API-Key: $API_KEY" http://localhost:8090/api/users
```

## 🔧 Configuration Files

### ZIVPN Config (`/etc/zivpn/config.json`)
```json
{
  "listen": ":5667",
  "cert": "/etc/zivpn/ssl/zivpn.crt",
  "key": "/etc/zivpn/ssl/zivpn.key",
  "obfs": "zivpn",
  "auth": {
    "mode": "passwords",
    "config": ["password1", "password2", "password3"]
  }
}
```

### UDPGW Config (`/etc/udpgw/udpgw.json`)
```json
{
  "LogLevel": "info",
  "LogFilename": "",
  "LogFileReopenRetries": null,
  "LogFileCreateMode": 438,
  "SkipPanickingLogWriter": false,
  "HostID": "zivpn-server",
  "UdpgwPort": 7300,
  "DNSResolverIPAddress": "8.8.8.8"
}
```

## 🌐 Port Configuration

| Port | Protocol | Service | Purpose |
|------|----------|---------|---------|
| 5667 | UDP | ZIVPN | Main VPN service |
| 7300 | UDP | UDPGW | UDP Gateway |
| 8080 | TCP | APK API | API for APK Manager |
| 8090 | TCP | Website API | API for Website Panel |
| 6000-19999 | UDP | ZIVPN | Client connection ports |

## 🔄 Cron Jobs

Auto-expiration job runs daily at midnight:
```bash
0 0 * * * /usr/bin/curl -s -X POST -H "X-API-Key: API_KEY" http://127.0.0.1:8090/api/cron/expire >> /var/log/zivpn-cron.log 2>&1
```

## 🗑 Uninstallation

### Using Script
```bash
sudo ./zivpn-complete.sh --uninstall
```

### Manual Uninstall
```bash
# Stop services
sudo systemctl stop zivpn udpgw zivpn-api zivpn-api-web
sudo systemctl disable zivpn udpgw zivpn-api zivpn-api-web

# Remove binaries
sudo rm -f /usr/local/bin/zivpn /usr/local/bin/udpgw

# Remove configuration
sudo rm -rf /etc/zivpn /etc/udpgw

# Remove systemd services
sudo rm -f /etc/systemd/system/zivpn.service \
           /etc/systemd/system/udpgw.service \
           /etc/systemd/system/zivpn-api.service \
           /etc/systemd/system/zivpn-api-web.service

# Reload systemd
sudo systemctl daemon-reload

# Remove cron job
(crontab -l | grep -v "/api/cron/expire") | crontab -
```

## 🐛 Troubleshooting

### Common Issues

#### 1. API Not Responding
```bash
# Check if service is running
sudo systemctl status zivpn-api-web

# Check logs
sudo journalctl -u zivpn-api-web -n 50 --no-pager

# Test port
sudo netstat -tlnp | grep 8090
```

#### 2. UDPGW Not Starting
```bash
# Check dependencies
which go

# Rebuild UDPGW
cd /tmp
git clone https://github.com/mukswilly/udpgw.git
cd udpgw
go build -o /usr/local/bin/udpgw
```

#### 3. SSL Certificate Issues
```bash
# Regenerate SSL
DOMAIN=$(cat /etc/zivpn/domain)
openssl req -new -newkey rsa:2048 -days 3650 -nodes -x509 \
  -subj "/C=ID/ST=Java/L=Jakarta/O=ZIVPN/CN=$DOMAIN" \
  -keyout /etc/zivpn/ssl/zivpn.key \
  -out /etc/zivpn/ssl/zivpn.crt

# Restart services
sudo systemctl restart zivpn
```

#### 4. Firewall Issues
```bash
# Check UFW status
sudo ufw status verbose

# Add missing ports
sudo ufw allow 8080/tcp
sudo ufw allow 8090/tcp
sudo ufw allow 5667/udp
sudo ufw allow 7300/udp

# Check iptables
sudo iptables -L -n -v
```

### Log Files
- **Installation log**: `/tmp/zivpn_install_*.log`
- **Cron job log**: `/var/log/zivpn-cron.log`
- **Service logs**: `journalctl -u SERVICE_NAME`

### Debug Mode
```bash
# Stop services first
sudo systemctl stop zivpn-api zivpn-api-web

# Run API in foreground
cd /etc/zivpn/api
./zivpn-api

# Or for website API
cd /etc/zivpn/api-web
./zivpn-api-web
```

## 🔐 Security Considerations

### 1. API Key Protection
- Change default API key immediately
- Store API key securely
- Use HTTPS in production

### 2. SSL Certificate
- Replace self-signed cert with valid SSL
- Use Let's Encrypt for free certificates

### 3. Firewall Configuration
- Restrict API ports to trusted IPs
- Use fail2ban for brute force protection
- Regular security updates

### 4. User Management
- Regularly rotate passwords
- Monitor user activity
- Set appropriate expiration dates

## 📈 Monitoring

### Basic Monitoring Commands
```bash
# Check active connections
ss -ulpn | grep 5667

# Monitor system resources
htop

# Check service status
watch -n 5 'systemctl status zivpn udpgw zivpn-api zivpn-api-web | grep -A 3 "Active:"'
```

### API Health Monitoring
```bash
# Create monitoring script
cat > /usr/local/bin/monitor-zivpn.sh << 'EOF'
#!/bin/bash
API_KEY=$(cat /etc/zivpn/apikey)
curl -s -H "X-API-Key: $API_KEY" http://localhost:8090/api/health | jq .
EOF
chmod +x /usr/local/bin/monitor-zivpn.sh
```

## 🤝 Support

### Getting Help
1. Check the troubleshooting section
2. Review service logs
3. Test individual components

### Reporting Issues
When reporting issues, include:
- Operating system and version
- Installation log
- Service status output
- Error messages

## 📄 License

This script is provided as-is without warranty. Use at your own risk.

## 🔄 Updates

### Update Procedure
```bash
# Backup configuration
sudo cp -r /etc/zivpn /etc/zivpn.backup

# Download new version
wget -O zivpn-complete-new.sh NEW_URL

# Compare and update
diff zivpn-complete.sh zivpn-complete-new.sh

# Run update
sudo ./zivpn-complete-new.sh
```

## 🎯 Quick Reference

| Command | Description |
|---------|-------------|
| `sudo ./zivpn-complete.sh` | Install all components |
| `sudo ./zivpn-complete.sh --test` | Test API connections |
| `sudo ./zivpn-complete.sh --uninstall` | Remove all components |
| `systemctl status zivpn` | Check ZIVPN status |
| `journalctl -u zivpn -f` | Follow ZIVPN logs |
| `curl localhost:8090/api/health` | Test website API |

---

**Note**: This documentation is for version 1.0 of ZIVPN Complete. Always refer to the latest version for updated features and security considerations.
