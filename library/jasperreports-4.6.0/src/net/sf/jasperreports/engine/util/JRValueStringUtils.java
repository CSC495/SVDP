/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package net.sf.jasperreports.engine.util;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.NotSerializableException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JRRuntimeException;

import org.w3c.tools.codec.Base64Decoder;
import org.w3c.tools.codec.Base64Encoder;
import org.w3c.tools.codec.Base64FormatException;


/**
 * Utility class used to serialize/deserialize value objects to/from String values.
 * <p>
 * Specific logic is used to convert to and from Strings values of the following types:
 * <ul>
 * 	<li><code>java.lang.String</code></li>
 * 	<li><code>java.lang.Character</code></li>
 * 	<li><code>java.lang.Boolean</code></li>
 * 	<li><code>java.lang.Byte</code></li>
 * 	<li><code>java.lang.Short</code></li>
 * 	<li><code>java.lang.Integer</code></li>
 * 	<li><code>java.lang.Long</code></li>
 * 	<li><code>java.lang.Float</code></li>
 * 	<li><code>java.lang.Double</code></li>
 * 	<li><code>java.math.BigInteger</code></li>
 * 	<li><code>java.math.BigDecimal</code></li>
 * 	<li><code>java.util.Date</code></li>
 * 	<li><code>java.sql.Timestamp</code></li>
 * 	<li><code>java.sql.Time</code></li>
 * </ul>
 * </p>
 * <p>
 * Object of other types are serialized and the resulting binary data is converted into a String
 * using the BASE64 encoding.
 * </p>
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRValueStringUtils.java 5180 2012-03-29 13:23:12Z teodord $
 */
public final class JRValueStringUtils
{
	
	protected static interface ValueSerializer
	{
		String serialize(Object value);
		
		Object deserialize(String data);
	}
	
	private static final Map<String,ValueSerializer> serializers;
	private static final ValueSerializer defaultSerializer;
	
	static
	{
		serializers = getSerializers();
		defaultSerializer = new DefaultSerializer();
	}
	
	/**
	 * Determines if there's a built-in serializer for the value type.
	 * 
	 * @param valueClass the value type
	 * @return whether the value type is handled by a built-in serializer
	 */
	public static boolean hasSerializer(String valueClass)
	{
		return serializers.containsKey(valueClass);
	}
	
	/**
	 * Converts a value into a String representation.
	 * 
	 * @param valueClass the type of the value
	 * @param value the value
	 * @return the String representation of the value
	 */
	public static String serialize(String valueClass, Object value)
	{
		String data;
		if (value == null)
		{
			data = null;
		}
		else
		{
			ValueSerializer serializer = getSerializer(valueClass);
			data = serializer.serialize(value);
		}
		return data;
	}

	
	/**
	 * Converts a String back into a value.
	 * 
	 * @param valueClass the type of the value
	 * @param data the String representation of the value
	 * @return the value
	 */
	public static Object deserialize(String valueClass, String data)
	{
		Object value;
		if (data == null)
		{
			value = null;
		}
		else
		{
			ValueSerializer serializer = getSerializer(valueClass);
			value = serializer.deserialize(data);
		}
		return value;
	}

	protected static ValueSerializer getSerializer(String valueClass)
	{
		ValueSerializer serializer = serializers.get(valueClass);
		if (serializer == null)
		{
			serializer = defaultSerializer;
		}
		return serializer;
	}

	
	private static Map<String,ValueSerializer> getSerializers()
	{
		Map<String,ValueSerializer> map = new HashMap<String,ValueSerializer>();
		map.put(java.lang.String.class.getName(), new StringSerializer());
		map.put(java.lang.Character.class.getName(), new CharacterSerializer());
		map.put(java.lang.Boolean.class.getName(), new BooleanSerializer());
		map.put(java.lang.Byte.class.getName(), new ByteSerializer());
		map.put(java.lang.Short.class.getName(), new ShortSerializer());
		map.put(java.lang.Integer.class.getName(), new IntegerSerializer());
		map.put(java.lang.Long.class.getName(), new LongSerializer());
		map.put(java.lang.Float.class.getName(), new FloatSerializer());
		map.put(java.lang.Double.class.getName(), new DoubleSerializer());
		map.put(java.math.BigInteger.class.getName(), new BigIntegerSerializer());
		map.put(java.math.BigDecimal.class.getName(), new BigDecimalSerializer());
		map.put(java.util.Date.class.getName(), new DateSerializer());
		map.put(java.sql.Timestamp.class.getName(), new TimestampSerializer());
		map.put(java.sql.Time.class.getName(), new TimeSerializer());
		return map;
	}
	
	
	protected static class StringSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			return data;
		}

		public String serialize(Object value)
		{
			return (String) value;
		}
	}
	
	
	protected static class CharacterSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			if (data.length() != 1)
			{
				throw new JRRuntimeException("Character data \"" + data + "\" should be exactly one character long");
			}
			return new Character(data.charAt(0));
		}

		public String serialize(Object value)
		{
			return String.valueOf(new char[]{((Character) value).charValue()});
		}
	}
	
	
	protected static class BooleanSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			if (data.equals("true"))
			{
				return Boolean.TRUE;
			}
			if (data.equals("false"))
			{
				return Boolean.FALSE;
			}
			throw new JRRuntimeException("Unkown boolean data \"" + data + "\"");
		}

		public String serialize(Object value)
		{
			return ((Boolean) value).booleanValue() ? "true" : "false";
		}
	}
	
	
	protected static class ByteSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return Byte.valueOf(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing Byte data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((Byte) value).toString();
		}
	}
	
	
	protected static class ShortSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return Short.valueOf(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing Short data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((Short) value).toString();
		}
	}
	
	
	protected static class IntegerSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return Integer.valueOf(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing Integer data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((Integer) value).toString();
		}
	}
	
	
	protected static class LongSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return Long.valueOf(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing Long data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((Long) value).toString();
		}
	}
	
	
	protected static class FloatSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return Float.valueOf(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing Float data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((Float) value).toString();
		}
	}
	
	
	protected static class DoubleSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return Double.valueOf(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing Double data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((Double) value).toString();
		}
	}
	
	
	protected static class BigIntegerSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return new java.math.BigInteger(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing BigInteger data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((java.math.BigInteger) value).toString();
		}
	}
	
	
	protected static class BigDecimalSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return new java.math.BigDecimal(data);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing BigDecimal data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return ((java.math.BigDecimal) value).toString();
		}
	}
	
	
	protected static class DateSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				long time = Long.parseLong(data);
				return new java.util.Date(time);
			}
			catch (NumberFormatException e)
			{
				throw new JRRuntimeException("Error parsing Date data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			return Long.toString(((java.util.Date) value).getTime());
		}
	}
	
	
	protected static class TimestampSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return java.sql.Timestamp.valueOf(data);
			}
			catch (IllegalArgumentException e)
			{
				throw new JRRuntimeException("Error parsing Timestamp data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			java.sql.Timestamp timestamp = (java.sql.Timestamp) value;
			return timestamp.toString();
		}
	}
	
	
	protected static class TimeSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				return java.sql.Time.valueOf(data);
			}
			catch (IllegalArgumentException e)
			{
				throw new JRRuntimeException("Error parsing Time data \"" + data + "\"", e);
			}
		}

		public String serialize(Object value)
		{
			java.sql.Time timestamp = (java.sql.Time) value;
			return timestamp.toString();
		}
	}
	
	
	protected static class DefaultSerializer implements ValueSerializer
	{
		public Object deserialize(String data)
		{
			try
			{
				ByteArrayInputStream dataIn = new ByteArrayInputStream(data.getBytes());
				ByteArrayOutputStream bytesOut = new ByteArrayOutputStream();
				Base64Decoder dec = new Base64Decoder(dataIn, bytesOut);
				dec.process();
				
				ByteArrayInputStream bytesIn = new ByteArrayInputStream(bytesOut.toByteArray());
				ObjectInputStream objectIn = new ObjectInputStream(bytesIn);
				return objectIn.readObject();
			}
			catch (IOException e)
			{
				throw new JRRuntimeException(e);
			}
			catch (ClassNotFoundException e)
			{
				throw new JRRuntimeException(e);
			}
			catch (Base64FormatException e)
			{
				throw new JRRuntimeException(e);
			}
		}

		public String serialize(Object value)
		{
			try
			{
				ByteArrayOutputStream bytesOut = new ByteArrayOutputStream();
				ObjectOutputStream objectOut = new ObjectOutputStream(bytesOut);
				objectOut.writeObject(value);
				objectOut.close();
				
				ByteArrayInputStream bytesIn = new ByteArrayInputStream(bytesOut.toByteArray());
				ByteArrayOutputStream dataOut = new ByteArrayOutputStream();				
				
				Base64Encoder enc = new Base64Encoder(bytesIn, dataOut);
				enc.process();
				
				return new String(dataOut.toByteArray(), "UTF-8");
			}
			catch (NotSerializableException e)
			{
				throw new JRRuntimeException("Value is not serializable", e);
			}
			catch (IOException e)
			{
				throw new JRRuntimeException(e);
			}
		}
	}
	
	
	private JRValueStringUtils()
	{
	}
}
