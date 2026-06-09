import http.server
import urllib.request
import urllib.error
import socketserver
import os
import sys
import traceback

PORT = 8000

# Set working directory to this script's directory
script_dir = os.path.dirname(os.path.abspath(__file__))
os.chdir(script_dir)

class ProxyHandler(http.server.SimpleHTTPRequestHandler):
    def log_message(self, format, *args):
        # Force stderr printing instantly
        sys.stderr.write("%s - - [%s] %s\n" %
                         (self.address_string(),
                          self.log_date_time_string(),
                          format%args))
        sys.stderr.flush()

    def proxy_request(self, method):
        target_url = f"https://api.postproxy.dev{self.path}"
        print(f"Proxying request: [{method}] {self.path} -> {target_url}", file=sys.stderr)
        
        # Read request body
        content_length = int(self.headers.get('Content-Length', 0))
        body = self.rfile.read(content_length) if content_length > 0 else None
        
        # Copy headers except Host and Connection
        headers = {}
        for key, val in self.headers.items():
            if key.lower() not in ('host', 'connection'):
                headers[key] = val
        
        req = urllib.request.Request(
            target_url,
            data=body,
            headers=headers,
            method=method
        )
        
        try:
            with urllib.request.urlopen(req) as response:
                status = response.status
                headers_list = response.getheaders()
                response_data = response.read()
                
                # Decompress if gzip encoded
                content_encoding = response.info().get('Content-Encoding', '')
                if 'gzip' in content_encoding:
                    try:
                        import gzip
                        response_data = gzip.decompress(response_data)
                    except Exception as decompression_error:
                        print(f"Failed to decompress gzip: {decompression_error}", file=sys.stderr)

                # Print first 500 chars of JSON response for field debugging
                print(f"Proxy Response Body (first 500 chars): {response_data[:500].decode('utf-8', errors='ignore')}", file=sys.stderr)
                
                self.send_response(status)
                for key, val in headers_list:
                    if key.lower() not in ('transfer-encoding', 'connection', 'content-encoding', 'content-length'):
                        self.send_header(key, val)
                self.send_header('Content-Length', str(len(response_data)))
                self.end_headers()
                self.wfile.write(response_data)
        except urllib.error.HTTPError as e:
            error_body = e.read()
            print(f"HTTPError in proxy: {e.code}. Response body: {error_body.decode('utf-8', errors='ignore')}", file=sys.stderr)
            self.send_response(e.code)
            for key, val in e.headers.items():
                if key.lower() not in ('transfer-encoding', 'connection'):
                    self.send_header(key, val)
            self.end_headers()
            self.wfile.write(error_body)
        except Exception as e:
            print(f"Error in proxy: {e}", file=sys.stderr)
            traceback.print_exc(file=sys.stderr)
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(f'{{"error": "{str(e)}"}}'.encode('utf-8'))

    def do_GET(self):
        try:
            if self.path.startswith('/api/'):
                self.proxy_request('GET')
            else:
                super().do_GET()
        except Exception as e:
            print(f"Exception in do_GET: {e}", file=sys.stderr)
            traceback.print_exc(file=sys.stderr)

    def do_POST(self):
        try:
            if self.path.startswith('/api/'):
                self.proxy_request('POST')
            else:
                self.send_error(405, "Method not allowed for static files")
        except Exception as e:
            print(f"Exception in do_POST: {e}", file=sys.stderr)
            traceback.print_exc(file=sys.stderr)

    def do_DELETE(self):
        try:
            if self.path.startswith('/api/'):
                self.proxy_request('DELETE')
            else:
                self.send_error(405, "Method not allowed for static files")
        except Exception as e:
            print(f"Exception in do_DELETE: {e}", file=sys.stderr)
            traceback.print_exc(file=sys.stderr)

    def do_PUT(self):
        try:
            if self.path.startswith('/api/'):
                self.proxy_request('PUT')
            else:
                self.send_error(405, "Method not allowed for static files")
        except Exception as e:
            print(f"Exception in do_PUT: {e}", file=sys.stderr)
            traceback.print_exc(file=sys.stderr)

    def do_OPTIONS(self):
        try:
            if self.path.startswith('/api/'):
                self.send_response(200)
                self.send_header('Access-Control-Allow-Origin', '*')
                self.send_header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS')
                self.send_header('Access-Control-Allow-Headers', 'Authorization, Content-Type')
                self.end_headers()
            else:
                super().do_OPTIONS()
        except Exception as e:
            print(f"Exception in do_OPTIONS: {e}", file=sys.stderr)
            traceback.print_exc(file=sys.stderr)

if __name__ == '__main__':
    # Force output flushing so logs are visible instantly
    sys.stdout.reconfigure(line_buffering=True)
    sys.stderr.reconfigure(line_buffering=True)
    
    # Allow address reuse
    http.server.HTTPServer.allow_reuse_address = True
    with http.server.HTTPServer(("", PORT), ProxyHandler) as httpd:
        print(f"Server started at http://localhost:{PORT} with API Proxy enabled.", file=sys.stderr)
        httpd.serve_forever()
