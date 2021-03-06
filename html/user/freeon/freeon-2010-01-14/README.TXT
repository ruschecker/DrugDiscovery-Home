********** Building FreeON **********

If you checked out the sources from the git repository, then you will have to
build the configure script before you can configure and make the sources. This
step has to be done only once however. After subsequent updates from git through
"git pull" you will need to only make "make clean" and then "make" again.

In order to build the configure script run the script

./reconfigure.sh

which executes a series of commands that generate the configure script at the
end. Now run the configure script. Run "./configure --help" to get a list of
options it understands.

./configure

Once the sources are configured run

make
make install

which will build the sources and install them. You should decide where you want
to have the binaries installed when you run the configure script. The default is
"/usr/local", i.e. the binaries are going to be installed in "/usr/local/bin".
You can change this by running configure as

./configure --prefix=/some/other/directory

which will install the binaries into /some/other/directory/bin.



********** Making a dist (a tar file) **********

The tar file is made with

make dist

and is uploaded to savannah with

make dist-upload

This will also generate a signature of the dist tar file using gpg. Please
verify that the dist works before uploading with

make distcheck



********** Cleaning the sources **********

To clean out the sources, there are several options:

make quickclean

will clean out most directories and avoid cleaning directories that are lengthy
to rebuild, as for instance Modules.

make clean

will clean everything, i.e. all of the .o files and such.

make distclean

will really clean stuff, the directory will look like as if just unpacked from
the tar archive.

make maintainer-clean

will go to town and clean out everything in addition to everything. This option
should really only be used by someone who knows how to live with the
consequences. It's not for the faint hearted.



********** Examples for configure **********

Some examples of how to configure FreeON. The following configure commands
work. In order to use the PHiPAC (P3) optimized dgemms, add --enable-phipac to
the configure command line.

(1)
./configure FC=/usr/local/gcc-4.2.1/bin/gfortran F77=/usr/local/gcc-4.2.1/bin/gfortran CC=/usr/local/gcc-4.2.1/bin/gcc LDFLAGS=-L/usr/local/lapack/lib LIBS="-llapack_linuxi386-gfortran-4.2.1 -lblas_linuxi386-gfortran-4.2.1" CFLAGS="-O3 -fomit-frame-pointer" FFLAGS="-O3 -fomit-frame-pointer" FCFLAGS="-O3 -fomit-frame-pointer" --prefix=/tmp/FreeON

(2)
./configure FC=/usr/local/gcc-4.2.1/bin/gfortran F77=/usr/local/gcc-4.2.1/bin/gfortran CC=/usr/local/gcc-4.2.1/bin/gcc LDFLAGS=-L/usr/local/lapack/lib LIBS="-llapack_linuxi386-gfortran-4.2.1 -lblas_linuxi386-gfortran-4.2.1" FFLAGS="-O0 -g" FCFLAGS="-O0 -g" CFLAGS="-O0 -g" --prefix=/tmp/FreeON

(3)
./configure FC=ifort F77=ifort CC=icc LDFLAGS=-L/usr/local/lapack/lib LIBS="-llapack_linux-ifort-10.0.023 -lblas_linux-ifort-10.0.023" CFLAGS="-O3 -fomit-frame-pointer" FFLAGS="-O3 -fomit-frame-pointer" FCFLAGS="-O3 -fomit-frame-pointer" --prefix=/tmp/FreeON

(4)
./configure FC=ifort F77=ifort CC=icc CFLAGS="-O3 -fomit-frame-pointer" FFLAGS="-O3 -fomit-frame-pointer" FCFLAGS="-O3 -fomit-frame-pointer" --prefix=/tmp/FreeON

(5)
./configure FC=/usr/local/gcc-4.2.1/bin/gfortran F77=/usr/local/gcc-4.2.1/bin/gfortran CC=/usr/local/gcc-4.2.1/bin/gcc CFLAGS="-O3 -fomit-frame-pointer" FFLAGS="-O3 -fomit-frame-pointer" FCFLAGS="-O3 -fomit-frame-pointer" --prefix=/tmp/FreeON --with-hdf5=/usr/local/hdf-1.4.5-post2

(6)
./configure FC=ifort F77=ifort CC=icc CFLAGS="-O3 -fomit-frame-pointer" FFLAGS="-O3 -fomit-frame-pointer" FCFLAGS="-O3 -fomit-frame-pointer" --prefix=/tmp/FreeON --disable-internal-lapack --with-lapack=/usr/local/lapack --with-lapacklibs="-llapack_linux-ifort-10.0.023 -lblas_linux-ifort-10.0.023"

(7)
./configure CC=/usr/local/gcc-4.2.4/bin/gcc FC=/usr/local/gcc-4.2.4/bin/gfortran F77=/usr/local/gcc-4.2.4/bin/gfortran CFLAGS="-O3 -msse -msse2 -mfpmath=sse -ftree-vectorize" FFLAGS="-O3 -msse -msse2 -mfpmath=sse -ftree-vectorize" FCFLAGS="-O3 -msse -msse2 -mfpmath=sse -ftree-vectorize" -disable-static-binaries --prefix=/tmp/FreeON

(8)
./configure FC=ifort F77=ifort CC=icc CFLAGS="-O3 -fomit-frame-pointer -xP" FFLAGS="-O3 -fomit-frame-pointer -xP" FCFLAGS="-O3 -fomit-frame-pointer -xP" --prefix=/tmp/FreeON



********** Timings **********

Some timing information:

Running examples/MD/MD-H2.inp on MacBook Pro 1 (Core Duo) 2.16GHz:

time (FreeON MD-H2.inp > /dev/null)

gfortran 4.2.1, compiled with (1): real 10m39.911s user 9m46.164s sys 0m15.639s
ifort 10.0.023, compiled with (3): real  9m10.989s user 8m14.051s sys 0m16.881s

time ( FreeON MD-H2-BO.inp > /dev/null )

gfortran 4.2.4, compiled with (7): real 3m24.527s user  2m56.920s sys 0m24.685s
ifort 10.1.015, compiled with (4): real 2m37.621s user  2m8.828s  sys 0m23.801s
ifort 10.1.015, compiled with (8): real 2m30.579s user  2m7.040s  sys 0m21.396s

Some building information:

Building (4) on MacBook Pro 1 (Core Duo) 2.16GHz
make -j1: real 23m6.268s  user 20m16.247s sys 1m38.732s
make -j2: real 15m24.275s user 23m26.838s sys 1m49.598s
make -j3: real 15m35.510s user 23m38.548s sys 1m49.434s




Running FreeON:

FreeON accepts several command line arguments. The most basic mode of running
FreeON is by specifying only the input file:

FreeON something.inp

The output files will be named based on the name of the input file with an added
PID (process ID) tag. One can also specify exactly what the output filename
should be called by running FreeON in this way:

FreeON something.inp something.out something.log something.geometries
