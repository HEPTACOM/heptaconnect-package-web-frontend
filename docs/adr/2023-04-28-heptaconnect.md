# 2023-04-28 - HEPTAconnect

## Context

[HEPTAconnect](https://heptaconnect.io) is a framework to build applications on a data flow between different APIs.
Its target audience is mainly developers.
These developers are eventually building solutions for non-technical humans.
Building UIs takes time and should not be done over and over again.


## Decision

We provide a way to simplify building UIs for non-technicians interacting with a HEPTAconnect portal node using the web browser.
To integrate well with HEPTAconnect projects the same [Architecture Decision Records](https://heptaconnect.io/reference/) are applied from HEPTAconnect core development.
Therefore this package follows the low-code approach like HEPTAconnect.
This UI has to be exchangeable per portal node as no Web UI development stack shall be enforced by the HEPTAconnect core packages.


## Consequences

### Pros

* This package integrates well into HEPTAconnect projects
* It is flexible in development and integration of different portals


### Cons

* This package will not be runnable well without HEPTAconnect
