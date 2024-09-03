#!/bin/bash

iptables -t mangle -F
iptables -t mangle -X

iptables -t mangle -N singbox-mark
iptables -t mangle -A singbox-mark -m addrtype --dst-type UNSPEC,LOCAL,ANYCAST,MULTICAST -j RETURN
iptables -t mangle -A singbox-mark -d 10.0.0.0/8 -j RETURN
iptables -t mangle -A singbox-mark -d 127.0.0.0/8 -j RETURN
iptables -t mangle -A singbox-mark -d 169.254.0.0/16 -j RETURN
iptables -t mangle -A singbox-mark -d 172.16.0.0/12 -j RETURN
iptables -t mangle -A singbox-mark -d 192.168.0.0/16 -j RETURN
iptables -t mangle -A singbox-mark -d 240.0.0.0/4 -j RETURN
iptables -t mangle -A singbox-mark -p udp --dport 123 -j RETURN
iptables -t mangle -A singbox-mark -j MARK --set-mark 1

iptables -t mangle -N singbox-tproxy
iptables -t mangle -A singbox-tproxy -m addrtype --dst-type UNSPEC,LOCAL,ANYCAST,MULTICAST -j RETURN
iptables -t mangle -A singbox-tproxy -d 10.0.0.0/8 -j RETURN
iptables -t mangle -A singbox-tproxy -d 127.0.0.0/8 -j RETURN
iptables -t mangle -A singbox-tproxy -d 169.254.0.0/16 -j RETURN
iptables -t mangle -A singbox-tproxy -d 172.16.0.0/12 -j RETURN
iptables -t mangle -A singbox-tproxy -d 192.168.0.0/16 -j RETURN
iptables -t mangle -A singbox-tproxy -d 240.0.0.0/4 -j RETURN
iptables -t mangle -A singbox-tproxy -p udp --dport 123 -j RETURN
iptables -t mangle -A singbox-tproxy -p tcp -j TPROXY --tproxy-mark 0x1/0x1 --on-port 9888
iptables -t mangle -A singbox-tproxy -p udp -j TPROXY --tproxy-mark 0x1/0x1 --on-port 9888

iptables -t mangle -A OUTPUT -p tcp -m cgroup ! --cgroup 1 -j singbox-mark
iptables -t mangle -A OUTPUT -p udp -m cgroup ! --cgroup 1 -j singbox-mark
iptables -t mangle -A PREROUTING -i lo -p tcp -j singbox-tproxy
iptables -t mangle -A PREROUTING -i lo -p udp -j singbox-tproxy
iptables -t mangle -A PREROUTING -i eth0 -p tcp -j singbox-tproxy
iptables -t mangle -A PREROUTING -i eth0 -p udp -j singbox-tproxy

/usr/bin/sing-box run -c /etc/neko/config/config.json