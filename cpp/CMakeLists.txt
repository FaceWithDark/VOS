# Specify the minimum version of CMake required to build this project
cmake_minimum_required(VERSION 3.10)

# Define the project name, version, and the languages used
project(VOTChatbot VERSION 0.1.0 LANGUAGES C CXX)

# Set the global C++ standard to C++23 and make it a requirement
set(CMAKE_CXX_STANDARD 23)
set(CMAKE_CXX_STANDARD_REQUIRED True)

# Enable the output of compile commands for tools like linters
set(CMAKE_EXPORT_COMPILE_COMMANDS ON)

# Detect if different types of build system is available and set the generator accordingly
if(EXISTS "${CMAKE_MAKE_PROGRAM}" AND "${CMAKE_MAKE_PROGRAM}" MATCHES "ninja")
    set(CMAKE_GENERATOR "Ninja")
elseif(EXISTS "${CMAKE_MAKE_PROGRAM}" AND "${CMAKE_MAKE_PROGRAM}" MATCHES "mingw32-make")
    set(CMAKE_GENERATOR "MinGW Makefiles")
elseif(EXISTS "${CMAKE_MAKE_PROGRAM}" AND "${CMAKE_MAKE_PROGRAM}" MATCHES "make")
    set(CMAKE_GENERATOR "MSYS Makefiles")
else()
    message(FATAL_ERROR "No suitable build system found. Please install Ninja, MinGW, or MSYS.")
endif()

# Declare an executable target built from the main implement cpp file
add_executable(vot_copilot main.cpp
                           sources/chatbot.cpp
              )

# Set the C++ standard to C++20 specifically for the named target
set_target_properties(vot_copilot PROPERTIES
  CMAKE_CXX_STANDARD 20
  CMAKE_CXX_STANDARD_REQUIRED True
)

# Set the path to the 'fmt' library's configuration file for CMake to find
set(fmt_DIR "$ENV{VCPKG_ROOT}/packages/fmt_x64-mingw-dynamic/share/fmt/fmt-config.cmake")

# Locate the 'fmt' package required for the project using vcpkg's configuration
find_package(fmt CONFIG REQUIRED)

# Link the 'fmt' library to the named target with private linkage
target_link_libraries(vot_copilot PRIVATE fmt::fmt)

# Add the includes directory to the search path
target_include_directories(vot_copilot PRIVATE ${CMAKE_SOURCE_DIR}/includes)

include(CTest)    # Include the CTest module to enable testing functionalities
enable_testing()  # Enable testing capabilities for this directory and below