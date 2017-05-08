# Port Mapping

As per convention, magedev configures port forwardings to default service ports. You may encounter errors like this indicating that ports are already in use, either by locally installed services or by another running project running with magedev.

    Error starting userland proxy: listen tcp 0.0.0.0:3306: bind: address already in use


you are only able to have one project running at a given time. But this constraint is ok, given the fact that everything else is automated for you. Please note, that `magedev` only works within your active project root folder. Trying to use it in a different folder or project will fail.
