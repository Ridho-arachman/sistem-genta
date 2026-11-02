@echo off
set JAVA_HOME=C:\Program Files\Microsoft\jdk-21.0.13.21-hotspot
set ANDROID_HOME=C:\Users\user\AppData\Local\Android\Sdk
set PATH=%JAVA_HOME%\bin;%ANDROID_HOME%\platform-tools;%ANDROID_HOME%\build-tools\34.0.0;%PATH%
cd /d D:\Coding\sistem-genta\android
gradlew.bat assembleRelease