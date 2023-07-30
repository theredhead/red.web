#!/bin/sh 

echo "Preparing directory..."

if [ -d build ]; then
    echo "removing directory"
    rm -rf build
fi

echo "creating directory"
mkdir build


echo "installing application"
cp -r demo/src build/app

echo "adding framework"
cp -r src/lib/* build/app/lib

echo "files copied."
