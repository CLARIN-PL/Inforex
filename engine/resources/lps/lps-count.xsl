<?xml version="1.0" encoding="ISO-8859-1"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/text/body">
<salute>
  <opener><xsl:value-of select="count(opener//salute)"/></opener>
  <closer><xsl:value-of select="count(closer//salute)"/></closer>
</salute>
</xsl:template>

</xsl:stylesheet>