vcl 4.1;

import std;

backend default {
    .host = "nginx";
    .port = "80";
}

acl purge {
	"fpm";
	"nginx";
}

sub vcl_recv {
    # Announce support for Edge Side Includes by setting the Surrogate-Capability header
    set req.http.Surrogate-Capability = "Varnish=ESI/1.0";

    # Remove empty query string parameters
    # e.g.: www.example.com/index.html?
    #if (req.url ~ "\?$") {
    #    set req.url = regsub(req.url, "\?$", "");
    #}

    # Remove port number from host header
    #set req.http.Host = regsub(req.http.Host, ":[0-9]+", "");

    # Sorts query string parameters alphabetically for cache normalization purposes
    #set req.url = std.querysort(req.url);
    # normalize url in case of leading HTTP scheme and domain
    set req.url = regsub(req.url, "^http[s]?://", "");

    # Remove the proxy header to mitigate the httpoxy vulnerability
    # See https://httpoxy.org/
    unset req.http.proxy;

    if(req.method == "PURGE") {
        if(!client.ip ~ purge) {
            return(synth(405, "PURGE not allowed"));
        }
        if (!req.http.X-Varnish-Tag-Pattern && !req.http.X-Varnish-Pool-Pattern) {
            return (synth(400, "X-Varnish-Tag-Pattern or X-Varnish-Pool-Pattern header required"));
        }
        if (req.http.X-Varnish-Tag-Pattern) {
          ban("obj.http.X-Varnish-Tag ~ " + req.http.X-Varnish-Tag-Pattern);
        }
        if (req.http.X-Varnish-Pool-Pattern) {
          ban("obj.http.X-Varnish-Pool ~ " + req.http.X-Varnish-Pool-Pattern);
        }
        return (synth(200, "Purged"));
    }
    
    # Only handle relevant HTTP request methods
    if (
        req.method != "GET" &&
        req.method != "HEAD" &&
        req.method != "PUT" &&
        req.method != "POST" &&
        req.method != "PATCH" &&
        req.method != "TRACE" &&
        req.method != "OPTIONS" &&
        req.method != "DELETE"
    ) {
        return (pipe);
    }

    # Remove tracking query string parameters used by analytics tools
    if (req.url ~ "(\?|&)(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=") {
        set req.url = regsuball(req.url, "&(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "");
        set req.url = regsuball(req.url, "\?(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "?");
        set req.url = regsub(req.url, "\?&", "?");
        set req.url = regsub(req.url, "\?$", "");
    }

    # Only cache GET and HEAD requests
    if ((req.method != "GET" && req.method != "HEAD") || req.http.Authorization) {
        return(pass);
    }

    # Mark static files with the X-Static-File header, and remove any cookies
    # X-Static-File is also used in vcl_backend_response to identify static files
    if (req.url ~ "^[^?]*\.(7z|avi|bmp|bz2|css|csv|doc|docx|eot|flac|flv|gif|gz|ico|jpeg|jpg|js|less|mka|mkv|mov|mp3|mp4|mpeg|mpg|odt|ogg|ogm|opus|otf|pdf|png|ppt|pptx|rar|rtf|svg|svgz|swf|tar|tbz|tgz|ttf|txt|txz|wav|webm|webp|woff|woff2|xls|xlsx|xml|xz|zip)(\?.*)?$") {
        set req.http.X-Static-File = "true";
        unset req.http.Cookie;
        return(hash);
    }

	# Remove all cookies
    if (req.http.Cookie) {
        unset req.http.cookie;
    }

    # Set initial grace period usage status
    set req.http.grace = "none";

    return(hash);
}

sub vcl_hash {
    # Create cache variations depending on the request protocol
    if (req.http.X-Forwarded-Proto) {
        hash_data(req.http.X-Forwarded-Proto);
    }
}

sub vcl_backend_response {
	# Serve stale content for x minutes after object expiration
	# Perform asynchronous revalidation while stale content is served
    # 10 minutes
    # set beresp.grace = 600s;
    set beresp.grace = 1h;
    # set beresp.grace = 1d;

    # If the file is marked as static we cache it for 1 day
    if (bereq.http.X-Static-File == "true") {
        unset beresp.http.Set-Cookie;
        set beresp.ttl = 1d;
    }

    # If we dont get a Cache-Control header from the backend
    # we default to 1h cache for all objects
    if (!beresp.http.Cache-Control) {
        set beresp.ttl = 1h;
    }

    # Parse Edge Side Include tags when the Surrogate-Control header contains ESI/1.0
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }
}

sub vcl_deliver {
    if (obj.hits) {
        set resp.http.X-Varnish-Cache-Debug = "HIT";
        set resp.http.grace = req.http.grace;
    } else {
        set resp.http.X-Varnish-Cache-Debug = "MISS";
    }

    # Cleanup of headers
    #unset resp.http.Age;
    #unset resp.http.X-Powered-By;
    #unset req.http.X-Static-File;
    #unset resp.http.X-Varnish-Tag;
    #unset resp.http.X-Varnish-Pool;
    #unset resp.http.Via;
}
