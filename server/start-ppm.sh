#!/bin/bash
ppm start --bootstrap=LegoAsync --bridge=LegoAsync\\Kernel\\PPM\\Bridge --workers=3 --debug=1  --port=2345 --max-execution-time=60 --max-requests=1000