/* ========================================================================== */
/*                                                                            */
/*   S0.h                                                                     */
/*   (c) 2001 Author                                                          */
/*                                                                            */
/*   Description                                                              */
/*                                                                            */
/* ========================================================================== */

/* enumerations */
typedef enum {
  log_error   = -1,
  log_warning =  0,
  log_info    =  1,
  log_debug   =  2
} log_level_t;

/* cmd line options */
typedef struct {
  char *device;
  int  resolution;
  int kilowatt;
char *log;
  int  verbosity;
  int  foreground;
} options_t;

#define SUCCESS 0

#ifndef TRUE
#define TRUE 1
#endif

#ifndef FALSE
#define FALSE 0
#endif