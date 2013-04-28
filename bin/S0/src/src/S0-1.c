/* ======================================================================== */
/*                                                                          */
/*   S0.c                                                                   */
/*   (c) 2013 Knut Kohl <github@knutkohl.de>                                */
/*                                                                          */
/*   Listen on serial port for S0 impulses                                  */
/*                                                                          */
/* ======================================================================== */

#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>
#include <stdarg.h>
#include <sys/ioctl.h>
#include <fcntl.h>
#include <errno.h>
#include <termios.h>
#include <signal.h>
#include <string.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/time.h>
#include <time.h>
#include <getopt.h>
#include <S0.h>

options_t options;

int fd;

void print(log_level_t level, const char *format, void *id, ... );
void bind_signals();
int config_parse_cli(int argc, char * argv[]);
void open_fd();
void daemonize();
void usage(char * argv[]);
void quit();

/*##########################################################################*/
int main(int argc, char **argv) {

  FILE *logfd;
  char buf[8];
  struct timeval t0, t1;
  double diff;

  /* bind signal handler */
  bind_signals("quit");

  /* parse command line and file options */
  config_parse_cli(argc, argv);

  /* prepare for background run */
  if (!options.foreground) {

    print(log_info, "Daemonize process...", NULL);

    daemonize();

    /* test open logfile */
    logfd = fopen(options.log, "a");

    if (logfd == NULL) {
      print(log_error, "Cannot open logfile %s: %s", NULL, options.log, strerror(errno));
      return EXIT_FAILURE;
    }

    print(log_debug, "Opened logfile %s", NULL, options.log);

    fclose(logfd);

  }

  /* open device */
  open_fd();

  /* init to detect & skip 1st impule */
  t0.tv_sec = FALSE;

  /* start listening */
  while (TRUE) {
    /* blocking until one character/pulse is read */
    read(fd, buf, 8);

    /* skip 1st impulse, results in "wrong" time differences/powers */
    if (t0.tv_sec == 0) {
      print(log_debug, "Skip 1st impulse", NULL);
      gettimeofday(&t0, NULL);
      continue;
    }

    /* calc time difference */
    gettimeofday(&t1 , NULL);

    diff = ( ( (double) t1.tv_sec*1000000 + (double) t1.tv_usec ) -
             ( (double) t0.tv_sec*1000000 + (double) t0.tv_usec ) ) / 1000000;

    /* calc consumption according based on time and resolution */
    if (!options.seconds) diff = 36e5 / diff / options.resolution;

    if (options.foreground == TRUE) {
      /* log data to console */
      print(log_warning, options.format, options.seconds ? "sec" : "W", diff);
    } else {
      /* open log file on each write, because it might be moved away ... */
      logfd = fopen(options.log, "a");
      if (logfd == NULL) {
        print(log_error, "Cannot open logfile %s: %s", NULL, options.log, strerror(errno));
        return EXIT_FAILURE;
      }
      fprintf(logfd, options.format, diff);
      fprintf(logfd, "\n");
      fflush(logfd);
      fclose(logfd);
    }

    /* remember timestamp for next cycle */
    t0 = t1;
  }
}

/**
 * Print error/warning/info/debug messages to stdout
 *
 * @param id could be NULL for general messages
 * @todo integrate into syslog
 */
void print(log_level_t level, const char *format, void *id, ... ) {
  if (level > options.verbosity) {
    return; /* skip message if its under the verbosity level */
  }

  struct timeval now;
  struct tm * timeinfo;
  char prefix[24];
  size_t pos = 0;

  gettimeofday(&now, NULL);
  timeinfo = localtime(&now.tv_sec);

  /* format timestamp */
  pos += strftime(prefix+pos, 18, "[%d-%b %H:%M:%S]", timeinfo);

  /* format section */
  if (id) {
    snprintf(prefix+pos, 8, "[%-3s]", (char *) id);
  }

  va_list args;
  va_start(args, id);

  /* print to stdout/stderr */
  if (getppid() != 1) { /* running as fork in background? */
    FILE *stream = (level > 0) ? stdout : stderr;
    fprintf(stream, "%-24s", prefix);
    vfprintf(stream, format, args);
    fprintf(stream, "\n");
  }
  va_end(args);
}

/**
 * Bind signals
 */
void bind_signals() {
  struct sigaction action;
  sigemptyset(&action.sa_mask);
  action.sa_flags = 0;
  action.sa_handler = quit;

  sigaction(SIGINT,  &action, NULL);  /* catch ctrl-c from terminal */
  sigaction(SIGHUP,  &action, NULL);  /* catch hangup signal */
  sigaction(SIGTERM, &action, NULL);  /* catch kill signal */
}

/**
 * Parse options from command line
 */
int config_parse_cli(int argc, char * argv[]) {
  /* set defaults */
  options.device = "";
  options.resolution = 1000;
  options.seconds = 0;
  options.format = "%.3f";
  options.log = "/tmp/S0.log";
  options.verbosity = 0;
  options.foreground = 0;

  int c;

  while ((c = getopt(argc, argv, "d:r:sf:l:vFh")) != -1) {

    switch (c) {
      case 'd':   options.device = optarg;             break;
      case 'r':   options.resolution = atoi(optarg);   break;
      case 's':   options.seconds = 1;                 break;
      case 'f':   options.format = optarg;             break;
      case 'l':   options.log = optarg;                break;
      case 'v':   options.verbosity += 1;              break;
      case 'F':   options.foreground = 1;              break;
      case '?':  case 'h':  default:
        printf("\n");
        usage(argv);
        exit((c == '?') ? EXIT_FAILURE : EXIT_SUCCESS);
    } // switch
  }

  if (!strcmp(options.device, "")) {
    usage(argv);
    exit(EXIT_FAILURE);
  }

  print(log_debug, "Device:     %s", "-d", options.device);
  print(log_debug, "Resolution: %d", "-r", options.resolution);
  print(log_debug, "Seconds:    %s", "-s", (options.seconds ? "yes" : "no"));
  print(log_debug, "Format:     %s", "-f", options.format);
  print(log_debug, "Log file:   %s", "-l", options.log);
  print(log_debug, "Verbosity:  %d", "-v", options.verbosity);
  print(log_debug, "Foreground: %s", "-F", (options.foreground ? "yes" : "no"));

  return SUCCESS;
}

/**
 * Open file descriptor
 */
void open_fd() {
  /* open device */
  if ((fd = open(options.device, O_RDWR | O_NOCTTY)) < 0) {
    printf("Error: Can't open device \"%s\".\n", options.device);
    exit(2);
  }

  /* configure port */
  struct termios tio;
  memset(&tio, 0, sizeof(struct termios));

  tio.c_cflag = B50 | CS7 | CLOCAL | CREAD;
  tio.c_iflag = IGNPAR | ISTRIP;
  tio.c_oflag = 0;
  tio.c_lflag = ISIG;
  tio.c_cc[VMIN] = 1;
  tio.c_cc[VTIME] = 1;

  /* apply configuration */
  tcflush(fd, TCIFLUSH);
  tcsetattr(fd, TCSANOW, &tio);
}

/**
 * Fork process to background
 *
 * @link http://www.enderunix.org/docs/eng/daemon.php
 */
void daemonize() {
  if (getppid() == 1) {
    return; /* already a daemon */
  }

  int i = fork();
  if (i < 0) {
    exit(EXIT_FAILURE); /* fork error */
  } else if (i > 0) {
    exit(EXIT_SUCCESS); /* parent exits */
  }

  /* child (daemon) continues */
  setsid(); /* obtain a new process group */

  for (i = getdtablesize(); i >= 0; --i) {
    close(i); /* close all descriptors */
  }

  /* handle standart I/O */
  i = open("/dev/null", O_RDWR);
  dup(i);
  dup(i);

  chdir("/");  /* change working directory */
  umask(0022); /* results in 755 */

  /* ignore signals from parent tty */
  struct sigaction action;
  sigemptyset(&action.sa_mask);
  action.sa_flags = 0;
  action.sa_handler = SIG_IGN;

  sigaction(SIGCHLD, &action, NULL); /* ignore child */
  sigaction(SIGTSTP, &action, NULL); /* ignore tty signals */
  sigaction(SIGTTOU, &action, NULL);
  sigaction(SIGTTIN, &action, NULL);
}

/**
 * Exit sequence
 */
void quit() {
  close(fd);
  exit(0);
}

/**
 * Usage
 */
void usage(char * argv[]) {
  printf("\n");
  printf("Usage: %s -d <device> [options]\n\n", argv[0]);
  printf("Options:\n\n");
  printf("    -d <device>       serial port device, required\n");
  printf("    -r <resolution>   impulses per kWh (kilo watt hour), default 1000\n");
  printf("    -s                output time difference between imulses instead of watt\n");
  printf("    -f <format>       output format, must fit printf(), default \"%%.3f\"\n");
  printf("    -l <filename>     log file, default \"/tmp/S0.log\"\n");
  printf("    -v                verbosity level: info\n");
  printf("    -vv               verbosity level: debug\n");
  printf("    -F                don't daemonize, run in foreground\n");
  printf("    -h                show this help\n");
  printf("\n");
  printf("If program runs in foreground, measuring data are written to stdout instead\n");
  printf("of log file.\n");
}

