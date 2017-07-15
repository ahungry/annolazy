#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

AC_PREREQ([2.69])
AC_INIT([ahungry/annolazy/php], [0.0.1], [m@ahungry.com])

# Checks for programs.
AC_CHECK_PROG(PHP, [php], [yes], [no])

AS_IF([test "x$PHP" = xno],
  AC_MSG_ERROR(Cannot find php))

AC_CHECK_PROG(COMPOSER, [composer], [yes], [no])

AS_IF([test "x$COMPOSER" = xno],
  AC_MSG_ERROR(Cannot find composer))

AC_CHECK_PROG(PHPUNIT, [phpunit], [yes], [no])

AS_IF([test "x$PHPUNIT" = xno],
  AC_MSG_ERROR(Cannot find phpunit))


# Checks for libraries.

# Checks for header files.

# Checks for typedefs, structures, and compiler characteristics.

# Checks for library functions.

AC_CONFIG_FILES([Makefile])
AC_OUTPUT
