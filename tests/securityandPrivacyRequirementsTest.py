# Run OWASP ZAP to test for vulnerabilities
zap-cli quick-scan --start-options "-config api.key=12345" "http://localhost/openemr"