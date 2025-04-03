<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
echo "<head>\n  <title>Block IP ";
echo myip();
echo "</title>\n</head>\n<style>\n    html,\nbody {\n  height: 100%;\n  width: 100%;\n  margin: 0;\n}\nbody {\n  background-color: #2c5a57;\n  background-image: linear-gradient(#101922, #275451);\n  background-size: 100%;\n  text-align: center;\n  line-height: 1.45;\n}\n\np {\n  text-transform: uppercase;\n  font-family: 'Montserrat', sans-serif;\n  font-weight: 600;\n  font-size: 26px;\n  letter-spacing: 4px;\n  color: white;\n  margin: 3em 0 1em;\n  opacity: 0;\n  animation: fadedown .5s ease-in-out;\n  animation-delay: 1s;\n  animation-fill-mode: forwards;\n}\n\nh3 {\n  color: #ecdb35;\n  font-family: 'squada one';\n  text-transform: uppercase;\n  font-size: 50px;\n  text-shadow: 0 3px red;\n  margin: 0;\n  transform: scale(0);\n  animation: zoomin 1s ease-in-out;\n  animation-delay: 2s;\n  animation-fill-mode: forwards;\n}\n\nh1 span {\n  display: inline-block;\n}\n\nh1 + p {\n  font-size: 20px;\n  letter-spacing: 2px;\n  max-width: 50%;\n  margin-left: auto;\n  margin-right: auto;\n  animation-delay: 3.5s;\n}\n\n@keyframes zoomin {\n  0% {\n    transform: scale(0);\n  }\n  90% {\n    transform: scale(1.1);\n  }\n  95% {\n    transform: scale(1.07);\n  }\n  100% {\n    transform: scale(1);\n  }\n}\n\n@keyframes fadedown {\n  0% {\n    opacity: 0;\n    transform: translate3d(0,-10px,0);\n  }\n  90% {\n    transform: translate3d(0,1px,0);\n  }\n  100% {\n    opacity: 1;\n    transform: translate3d(0,0,0);\n  }\n}\n</style>\n<p>";
echo __("Welcome to 403:");
echo "</p>\n<h3>";
echo __("ĐỊA CHỈ IP " . myip() . " CỦA BẠN ĐÃ BỊ CHẶN KHỎI HỆ THỐNG");
echo "</h3>\n";
echo $CMSNT->site("page_block_ip");
echo "\n<script>\n    function norm(value, min, max) {\n  return (value - min) / (max - min);\n}\n\nfunction lerp(norm, min, max) {\n  return (max - min) * norm + min;\n}\n\nfunction map(value, sourceMin, sourceMax, destMin, destMax) {\n  return lerp(norm(value, sourceMin, sourceMax), destMin, destMax);\n}\n\nfunction map2(value, sourceMin, sourceMax, destMin, destMax, percent) {\n  return percent <= 0.5\n    ? map(value, sourceMin, sourceMax, destMin, destMax)\n    : map(value, sourceMin, sourceMax, destMax, destMin);\n}\n\nfunction fisheye(el) {\n  let text = el.innerText.trim(),\n    numberOfChars = text.length;\n\n  el.innerHTML =\n    \"<span>\" +\n    text\n      .split(\"\")\n      .map(c => {\n        return c === \" \" ? \"&nbsp;\" : c;\n      })\n      .join(\"</span><span>\") +\n    \"</span>\";\n\n  el.querySelectorAll(\"span\").forEach((c, i) => {\n    const skew = map(i, 0, numberOfChars - 1, -15, 15),\n      scale = map2(i, 0, numberOfChars - 1, 1, 3, i / numberOfChars),\n      letterSpace = map2(i, 0, numberOfChars - 1, 5, 20, i / numberOfChars);\n\n    c.style.transform = \"skew(\" + skew + \"deg) scale(1, \" + scale + \")\";\n    c.style.letterSpacing = letterSpace + \"px\";\n  });\n}\n\nfisheye(document.querySelector(\"h1\"));\n\n</script>";

?>